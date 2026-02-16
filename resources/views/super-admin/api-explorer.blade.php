<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🧪 {{ __('API Explorer') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="apiExplorer()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Header Info --}}
            <div class="mb-6 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">System API Explorer</h3>
                        <p class="text-indigo-100 mt-1">Discover, test, and debug all system endpoints in one place.</p>
                    </div>
                    <div class="text-right text-sm text-indigo-200">
                        <div>Total Endpoints: <span
                                class="font-bold text-white">{{ collect($endpoints)->sum(fn($g) => count($g['endpoints'])) }}</span>
                        </div>
                        <div>Categories: <span class="font-bold text-white">{{ count($endpoints) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Endpoint List --}}
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <input type="text" x-model="searchQuery" placeholder="🔍 Search endpoints..."
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 mb-3">

                        @foreach ($endpoints as $groupIndex => $group)
                            <div class="mb-4" x-show="filterGroup({{ $groupIndex }})">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    {{ $group['icon'] }} {{ $group['category'] }}
                                </h4>
                                @foreach ($group['endpoints'] as $epIndex => $endpoint)
                                    <button
                                        x-show="filterEndpoint('{{ strtolower($endpoint['name']) }}', '{{ strtolower($endpoint['url']) }}')"
                                        @click="selectEndpoint({{ $groupIndex }}, {{ $epIndex }})" :class="selectedGroup === {{ $groupIndex }} && selectedEndpoint === {{ $epIndex }}
                                                    ? 'bg-indigo-50 border-indigo-400 text-indigo-700'
                                                    : 'bg-white border-gray-200 hover:bg-gray-50 text-gray-700'"
                                        class="w-full text-left px-3 py-2 rounded-md border mb-1 transition-all text-sm flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                                                    {{ $endpoint['method'] === 'GET' ? 'bg-green-100 text-green-700' : '' }}
                                                    {{ $endpoint['method'] === 'POST' ? 'bg-blue-100 text-blue-700' : '' }}
                                                    {{ $endpoint['method'] === 'PATCH' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                    {{ $endpoint['method'] === 'DELETE' ? 'bg-red-100 text-red-700' : '' }}">
                                            {{ $endpoint['method'] }}
                                        </span>
                                        <span class="truncate">{{ $endpoint['name'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Detail & Execution Panel --}}
                <div class="lg:col-span-2">
                    <template x-if="currentEndpoint">
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                            {{-- Endpoint Header --}}
                            <div class="p-6 border-b border-gray-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-bold"
                                        :class="{
                                            'bg-green-100 text-green-700': currentEndpoint.method === 'GET',
                                            'bg-blue-100 text-blue-700': currentEndpoint.method === 'POST',
                                            'bg-yellow-100 text-yellow-700': currentEndpoint.method === 'PATCH',
                                            'bg-red-100 text-red-700': currentEndpoint.method === 'DELETE',
                                        }" x-text="currentEndpoint.method"></span>
                                    <code class="text-lg font-mono text-gray-800" x-text="currentEndpoint.url"></code>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900" x-text="currentEndpoint.name"></h3>
                                <p class="text-gray-500 mt-1 text-sm" x-text="currentEndpoint.description"></p>
                            </div>

                            {{-- Parameters --}}
                            <div class="p-6 border-b border-gray-100"
                                x-show="currentEndpoint.params && currentEndpoint.params.length > 0">
                                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Parameters
                                </h4>
                                <div class="space-y-3">
                                    <template x-for="(param, idx) in currentEndpoint.params" :key="idx">
                                        <div class="flex items-start gap-4 bg-gray-50 rounded-md p-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <code class="text-sm font-mono font-bold text-indigo-600"
                                                        x-text="param.name"></code>
                                                    <span
                                                        class="text-xs px-1.5 py-0.5 rounded bg-gray-200 text-gray-600"
                                                        x-text="param.type"></span>
                                                    <span x-show="param.required"
                                                        class="text-xs px-1.5 py-0.5 rounded bg-red-100 text-red-600">required</span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1" x-text="param.description"></p>
                                            </div>
                                            <div class="w-48">
                                                <input type="text" :id="'param-' + param.name"
                                                    x-model="paramValues[param.name]" :placeholder="param.name"
                                                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Request Body --}}
                            <div class="p-6 border-b border-gray-100"
                                x-show="currentEndpoint.method === 'POST' || currentEndpoint.method === 'PATCH'">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Request Body
                                        (JSON)</h4>
                                    <button x-show="currentEndpoint.example_body"
                                        @click="requestBody = currentEndpoint.example_body"
                                        class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-md hover:bg-indigo-200 transition font-medium">
                                        📋 Fill Example
                                    </button>
                                </div>
                                <textarea x-model="requestBody" rows="8"
                                    class="w-full font-mono text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-900 text-green-400 p-4"
                                    placeholder='{ "key": "value" }'></textarea>
                            </div>

                            {{-- Execute Button --}}
                            <div class="p-6 border-b border-gray-100 flex items-center gap-4">
                                <button @click="executeRequest()" :disabled="isLoading"
                                    class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <template x-if="isLoading">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </template>
                                    <span x-text="isLoading ? 'Sending...' : '🚀 Send Request'"></span>
                                </button>

                                <span x-show="responseTime" class="text-sm text-gray-500">
                                    ⏱️ <span x-text="responseTime"></span>ms
                                </span>
                            </div>

                            {{-- Response --}}
                            <div class="p-6" x-show="response !== null">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Response</h4>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-bold px-3 py-1 rounded-full" :class="{
                                                'bg-green-100 text-green-700': response && response.status >= 200 && response.status < 300,
                                                'bg-yellow-100 text-yellow-700': response && response.status >= 300 && response.status < 400,
                                                'bg-red-100 text-red-700': response && response.status >= 400,
                                            }" x-text="response ? response.status : ''"></span>
                                        <button @click="copyResponse()"
                                            class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-md hover:bg-gray-200 transition">
                                            📋 Copy
                                        </button>
                                    </div>
                                </div>
                                <pre class="bg-gray-900 text-green-400 rounded-lg p-4 overflow-x-auto text-sm font-mono max-h-96 overflow-y-auto"
                                    x-text="formatResponse()"></pre>
                            </div>
                        </div>
                    </template>

                    {{-- Empty State --}}
                    <template x-if="!currentEndpoint">
                        <div class="bg-white shadow-sm rounded-lg p-12 text-center">
                            <div class="text-6xl mb-4">🛠️</div>
                            <h3 class="text-xl font-bold text-gray-700 mb-2">Select an Endpoint</h3>
                            <p class="text-gray-500">Choose an API endpoint from the left panel to view its details,
                                parameters, and send test requests.</p>
                        </div>
                    </template>
                </div>

            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function apiExplorer() {
                const endpoints = @json($endpoints);

                return {
                    endpoints: endpoints,
                    selectedGroup: null,
                    selectedEndpoint: null,
                    currentEndpoint: null,
                    paramValues: {},
                    requestBody: '',
                    response: null,
                    responseTime: null,
                    isLoading: false,
                    searchQuery: '',

                    selectEndpoint(groupIdx, epIdx) {
                        this.selectedGroup = groupIdx;
                        this.selectedEndpoint = epIdx;
                        this.currentEndpoint = this.endpoints[groupIdx].endpoints[epIdx];
                        this.paramValues = {};
                        this.requestBody = '';
                        this.response = null;
                        this.responseTime = null;

                        // Pre-fill example body
                        if (this.currentEndpoint.example_body) {
                            this.requestBody = this.currentEndpoint.example_body;
                        }
                    },

                    filterGroup(groupIdx) {
                        if (!this.searchQuery) return true;
                        const q = this.searchQuery.toLowerCase();
                        return this.endpoints[groupIdx].endpoints.some(ep =>
                            ep.name.toLowerCase().includes(q) || ep.url.toLowerCase().includes(q)
                        );
                    },

                    filterEndpoint(name, url) {
                        if (!this.searchQuery) return true;
                        const q = this.searchQuery.toLowerCase();
                        return name.includes(q) || url.includes(q);
                    },

                    async executeRequest() {
                        if (!this.currentEndpoint) return;

                        this.isLoading = true;
                        this.response = null;
                        const startTime = performance.now();

                        let url = this.currentEndpoint.url;

                        // Append query params for GET
                        if (this.currentEndpoint.method === 'GET' && Object.keys(this.paramValues).length > 0) {
                            const params = new URLSearchParams();
                            for (const [key, value] of Object.entries(this.paramValues)) {
                                if (value) params.append(key, value);
                            }
                            const qs = params.toString();
                            if (qs) url += '?' + qs;
                        }

                        try {
                            const res = await fetch('/super-admin/api-explorer/execute', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    method: this.currentEndpoint.method,
                                    url: url,
                                    body: this.requestBody || null,
                                }),
                            });

                            this.response = await res.json();
                        } catch (err) {
                            this.response = {
                                status: 0,
                                body: { error: err.message },
                                is_json: true,
                            };
                        }

                        this.responseTime = Math.round(performance.now() - startTime);
                        this.isLoading = false;
                    },

                    formatResponse() {
                        if (!this.response) return '';
                        if (this.response.is_json) {
                            return JSON.stringify(this.response.body, null, 2);
                        }
                        // For HTML responses, just show a summary
                        const body = this.response.body || '';
                        if (typeof body === 'string' && body.length > 500) {
                            return body.substring(0, 500) + '\n\n... (HTML response truncated, view in browser)';
                        }
                        return typeof body === 'string' ? body : JSON.stringify(body, null, 2);
                    },

                    copyResponse() {
                        const text = this.formatResponse();
                        navigator.clipboard.writeText(text);
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>