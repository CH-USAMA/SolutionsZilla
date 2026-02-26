const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const dotenv = require('dotenv');
const axios = require('axios');
const fs = require('fs');
const path = require('path');

dotenv.config();

const LOG_FILE = path.join(__dirname, 'debug.log');
const logToFile = (msg, extra = '') => {
    try {
        const timestamp = new Date().toISOString();
        let content = msg;

        if (extra) {
            if (extra instanceof Error) {
                content += ` Error: ${extra.message} \nStack: ${extra.stack}`;
            } else {
                content += ` ${JSON.stringify(extra)}`;
            }
        }

        fs.appendFileSync(LOG_FILE, `[${timestamp}] ${content}\n`);
        console.log(`[LOG] ${content}`);
    } catch (err) {
        console.log(`[FILE LOG ERROR] ${err.message}`);
        console.log(msg, extra);
    }
};

const app = express();
const port = process.env.PORT || 3000;
const API_KEY = process.env.WHATSAPP_JS_API_KEY || '';

app.use(cors());
app.use(bodyParser.json());

// API Key Middleware
const authMiddleware = (req, res, next) => {
    logToFile(`[INCOMING] ${req.method} ${req.url}`);
    if (API_KEY) {
        const apiKeyHeader = req.headers['x-api-key'];
        if (apiKeyHeader !== API_KEY) {
            logToFile(`[AUTH FAILED] Invalid API Key from ${req.ip}`);
            return res.status(403).json({ error: 'Unauthorized: Invalid API Key' });
        }
    }
    next();
};

const sessions = new Map();

/**
 * Initialize a session for a clinic
 */
const initSession = (sessionId) => {
    if (sessions.has(sessionId)) return sessions.get(sessionId);

    logToFile(`Starting session for: ${sessionId}`);

    const client = new Client({
        authStrategy: new LocalAuth({
            clientId: sessionId
        }),
        puppeteer: {
            headless: 'new',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu'
            ]
        }
    });

    const sessionData = {
        client,
        status: 'initializing',
        qr: null
    };

    client.on('qr', (qr) => {
        logToFile(`QR RECEIVED for ${sessionId}`);
        sessionData.qr = qr;
        sessionData.status = 'awaiting_scan';
    });

    client.on('message', async (msg) => {
        logToFile(`MESSAGE RECEIVED for ${sessionId} from ${msg.from}`);

        const webhookUrl = process.env.WHATSAPP_WEBHOOK_URL;
        if (webhookUrl) {
            const bridgePayload = {
                typeWebhook: 'incomingMessageReceived',
                instanceData: {
                    idInstance: sessionId,
                    wid: client.info?.wid?._serialized || ''
                },
                senderData: {
                    sender: msg.from
                },
                messageData: {
                    typeMessage: msg.type === 'chat' ? 'textMessage' : msg.type,
                    textMessageData: {
                        textMessage: msg.body
                    }
                },
                idMessage: msg.id.id,
                timestamp: msg.timestamp
            };

            try {
                await axios.post(webhookUrl, bridgePayload, {
                    headers: { 'x-api-key': API_KEY }
                });
            } catch (err) {
                logToFile('Failed to send webhook:', err.message);
            }
        }
    });

    client.on('ready', () => {
        logToFile(`Client ${sessionId} is ready!`);
        sessionData.status = 'connected';
        sessionData.qr = null;
    });

    client.on('authenticated', () => {
        logToFile(`Client ${sessionId} authenticated`);
        sessionData.status = 'authenticated';
    });

    client.on('auth_failure', (msg) => {
        logToFile(`Client ${sessionId} auth failure: ${msg}`);
        sessionData.status = 'auth_failure';
    });

    client.on('disconnected', (reason) => {
        logToFile(`Client ${sessionId} was logged out`, reason);
        sessionData.status = 'disconnected';
        sessions.delete(sessionId);
    });

    client.initialize().then(() => {
        logToFile(`Initialize call success for ${sessionId}`);
    }).catch(err => {
        logToFile(`FAILED TO INITIALIZE ${sessionId}:`, err);
        sessionData.status = 'failed';
    });
    sessions.set(sessionId, sessionData);
    return sessionData;
};

// -- API ENDPOINTS --

/**
 * Add / Start a session
 */
app.post('/sessions/add', authMiddleware, (req, res) => {
    const { session } = req.body;
    if (!session) return res.status(400).json({ error: 'Session ID is required' });

    initSession(session);
    res.json({ status: 'initializing', message: `Session ${session} initializaion started` });
});

/**
 * Get session status
 */
app.get('/sessions/status/:session', authMiddleware, (req, res) => {
    const sessionId = req.params.session;
    const sessionData = sessions.get(sessionId);

    if (!sessionData) {
        return res.json({ status: 'disconnected', details: 'Session not initialized' });
    }

    res.json({ status: sessionData.status });
});

/**
 * Get QR code as Base64 image string
 */
app.get('/sessions/qr/:session', authMiddleware, async (req, res) => {
    const sessionId = req.params.session;
    logToFile(`[ENDPOINT] GET /sessions/qr/${sessionId} reached`);
    let sessionData = sessions.get(sessionId);

    if (!sessionData) {
        sessionData = initSession(sessionId);
    }

    if (sessionData.status === 'connected') {
        return res.json({ status: 'connected', message: 'Already connected' });
    }

    if (sessionData.qr) {
        // We return the raw string, the Laravel app can wrap it in data:image/png;base64,... if needed
        // But for better integration, let's return a protocol-friendly version if possible 
        // Or just the raw QR string which we can convert on Laravel side.
        // JsApiProvider.php expects a QR string from data.qr

        const QRCode = require('qrcode');
        try {
            const qrImage = await QRCode.toDataURL(sessionData.qr);
            res.json({ qr: qrImage });
        } catch (err) {
            res.status(500).json({ error: 'Failed to generate QR image' });
        }
    } else {
        res.json({ status: sessionData.status, message: 'QR not yet ready, please wait...' });
    }
});

/**
 * Logout / Disconnect a session
 */
app.post('/sessions/logout/:session', authMiddleware, async (req, res) => {
    const sessionId = req.params.session;
    logToFile(`[ENDPOINT] POST /sessions/logout/${sessionId}`);
    const sessionData = sessions.get(sessionId);

    if (!sessionData) {
        return res.json({ status: 'disconnected', message: 'Session was not active' });
    }

    try {
        if (sessionData.client) {
            await sessionData.client.logout();
            await sessionData.client.destroy();
        }
        sessions.delete(sessionId);

        // Remove local auth folder for clean re-login
        const authPath = path.join(__dirname, '.wwebjs_auth', `session-${sessionId}`);
        if (fs.existsSync(authPath)) {
            fs.rmSync(authPath, { recursive: true, force: true });
            logToFile(`Removed auth data for ${sessionId}`);
        }

        logToFile(`Session ${sessionId} logged out successfully`);
        res.json({ status: 'disconnected', message: 'Logged out successfully' });
    } catch (err) {
        logToFile(`Error logging out ${sessionId}:`, err);
        sessions.delete(sessionId);
        res.json({ status: 'disconnected', message: 'Session cleared (with errors)' });
    }
});

/**
 * Send a message
 */
app.post('/messages/send', authMiddleware, async (req, res) => {
    const { session, to, text } = req.body;

    if (!session || !to || !text) {
        return res.status(400).json({ error: 'Missing parameters (session, to, text)' });
    }

    const sessionData = sessions.get(session);
    if (!sessionData || sessionData.status !== 'connected') {
        return res.status(400).json({ error: 'Session not connected' });
    }

    try {
        // Format phone: remove any + and spaces, ensure it ends with @c.us
        let chatId = to.replace(/\D/g, '');
        if (!chatId.endsWith('@c.us')) {
            chatId += '@c.us';
        }

        const msg = await sessionData.client.sendMessage(chatId, text);
        res.json({ status: 'sent', messageId: msg.id.id });
    } catch (err) {
        console.error('Send error:', err);
        res.status(500).json({ error: 'Failed to send message', details: err.message });
    }
});

app.listen(port, () => {
    logToFile(`=================================================`);
    logToFile(`  WhatsApp Gateway is RUNNING on port ${port}`);
    logToFile(`  URL: http://localhost:${port}`);
    logToFile(`=================================================`);
});
