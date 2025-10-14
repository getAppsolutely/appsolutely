<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Design your "{{ $page->name }}" Page - Page Builder</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Page Builder Assets (includes GrapesJS, FontAwesome, TailwindCSS) -->
    @vite(['resources/page-builder/assets/scss/app.scss', 'resources/page-builder/assets/ts/app.ts'], 'build/page-builder')
</head>

<body class="bg-slate-50 text-slate-800">
    <!-- Drag and Drop Overlay -->
    <div class="drag-overlay" id="drag-overlay">
        <div class="drag-placeholder">Drag and drop blocks here</div>
    </div>

    <!-- Top Toolbar -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white shadow-md">
        <div class="mx-auto px-4 py-2 flex items-center justify-between">
            <!-- Left: Logo -->
            <div class="flex items-center">
                <div class="bg-primary w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-magic text-white"></i>
                </div>
                <h1 class="text-xl font-bold text-slate-800">Page Builder</h1>
            </div>

            <!-- Center: Device Preview -->
            <div class="flex items-center bg-slate-100 rounded-lg p-1">
                <button class="device-btn px-4 py-1 rounded-md text-sm active" data-device="desktop">
                    <i class="fas fa-desktop mr-2"></i>Desktop
                </button>
                <button class="device-btn px-4 py-1 rounded-md text-sm ml-1" data-device="tablet">
                    <i class="fas fa-tablet-alt mr-2"></i>Tablet
                </button>
                <button class="device-btn px-4 py-1 rounded-md text-sm ml-1" data-device="mobile">
                    <i class="fas fa-mobile-alt mr-2"></i>Mobile
                </button>
            </div>

            <!-- Right: Action Buttons -->
            <div class="flex space-x-2">
                <div class="text-sm text-slate-500 mr-5">
                    <i class="fas fa-cube mr-1 mt-2"></i><span id="block-count">0</span> blocks
                </div>
                <button id="save-config-btn"
                        class="flex items-center bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-md text-sm">
                    <i class="fas fa-undo mr-2"></i>Config Save
                </button>
                <button id="undo-btn"
                        class="flex items-center bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-md text-sm">
                    <i class="fas fa-undo mr-2"></i>Undo
                </button>
                <button id="redo-btn"
                        class="flex items-center bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-md text-sm">
                    <i class="fas fa-redo mr-2"></i>Redo
                </button>
                <button id="reset-btn"
                        class="flex items-center bg-slate-100 hover:bg-red-200 px-3 py-1 rounded-md text-sm">
                    <i class="fas fa-eraser mr-2"></i>Reset
                </button>
                <button id="save-btn"
                        class="flex items-center bg-primary hover:bg-indigo-600 px-4 py-1 rounded-md text-sm text-white">
                    <i class="fas fa-save mr-2"></i>Save
                </button>
                <button id="preview-btn"
                        class="flex items-center bg-secondary hover:bg-purple-600 px-4 py-1 rounded-md text-sm text-white">
                    <i class="fas fa-eye mr-2"></i>Preview
                </button>
            </div>
        </div>
    </header>

    <main class="pt-20 mx-auto flex grapes-editor">
        <section class="flex-1 shadow-lg rounded-lg mr-4 px-6 editor-canvas-wrapper">
            <div id="editor-canvas" class="!bg-none !bg-white mb-4 px-8 pt-8 border-slate-300 rounded-lg">
            </div>
        </section>
        <aside class="w-80 bg-white shadow-lg rounded-lg p-4 overflow-y-auto">
            <div class="flex border-b border-slate-200 mb-4">
                <button id="blocks-tab" class="tab-btn active py-2 px-4 font-medium">Blocks</button>
                <button id="config-tab" class="tab-btn py-2 px-4 font-medium">Config</button>
            </div>

            <div id="blocks-content" class="config-panel">
                <h2 class="text-lg font-semibold mb-4 text-slate-700">
                    <i class="fas fa-boxes mr-2 text-secondary"></i>Blocks
                </h2>

                <div class="relative mb-4 hidden">
                    <input type="text" placeholder="Search blocks..."
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                    <i class="fas fa-search absolute right-3 top-3 text-slate-400"></i>
                </div>

                <div class="gjs-blocks-c space-y-6 !bg-white" id="blocks">
                </div>
            </div>

            <!-- Configuration Panel Content -->
            <div id="config-content" class="config-panel">
                <h2 class="text-lg font-semibold mb-4 text-slate-700">
                    <i class="fas fa-cog mr-2 text-primary"></i>Config Panel
                </h2>

                <div id="config-form" class="space-y-6">
                    <div class="text-center py-10 text-slate-400">
                        <i class="fas fa-mouse-pointer text-4xl mb-3"></i>
                        <p>Please select a block</p>
                    </div>
                </div>
            </div>
        </aside>
    </main>

    <!-- Preview Modal -->
    <div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-75 items-center justify-center z-50 hidden pt-[10vh]">
        <div class="bg-white rounded-lg w-11/12 mx-auto h-11/12 my-auto overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Preview</h3>
                <button id="close-preview" class="text-slate-500 hover:text-slate-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="h-full p-4 overflow-auto" id="preview-content">
            </div>
        </div>
    </div>

    <!-- Page Builder Initialization -->
    <script>
        // Initialize Page Builder with data
        window.pageBuilderData = @json(json_encode($page['setting']));
        window.pageBuilderConfig = {
            blockRegistryUrl: '{{ admin_route('api.pages.block-registry') }}',
            saveUrl: '{{ admin_route('api.pages.save',[$reference]) }}',
            resetUrl: '{{ admin_route('api.pages.reset',[$reference]) }}',
            dataUrl: '{{ admin_route('api.pages.data',[$reference]) }}',
            themedStyles: @json(themed_styles())
        };
    </script>
</body>
</html>
