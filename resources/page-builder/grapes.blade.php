<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Design your "{{ $page->name }}" Page - Page Builder</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#1e293b',
                        editor: '#f8fafc'
                    },
                    height: {
                        '11/12': '91.666667%',
                    },
                }
            }
        }
    </script>
    <style>
        /* Reset some default styling */
        .gjs-cv-canvas {
            top: 0;
            width: 100%;
            height: 100%;
        }

        .gjs-block {
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: #ffffff !important;
            width: 100%;
        }

        .gjs-block:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .gjs-editor, .gjs-title, .gjs-blocks-c, .gjs-cv-canvas-bg {
            background-color: #ffffff !important;
            color: #1e293b !important;
            font-weight: bold;
        }

        .gjs-block-category.gjs-open {
            border-bottom: none;
        }

        .editor-canvas-wrapper {
            background-color: #444;
        }

        #editor-canvas {
            background-color: #f8fafc;
            background-image: linear-gradient(#e2e8f0 1px, transparent 1px), linear-gradient(90deg, #e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
            min-height: 500px;
        }

        .device-btn.active {
            background-color: #6366f1;
            color: white;
        }

        .config-panel {
            max-height: calc(100vh - 180px);
            overflow-y: auto;
        }

        .grapes-editor {
            height: calc(100vh - 4rem);
            background-color: #444;
        }

        .drag-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(99, 102, 241, 0.1);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            pointer-events: none;
        }

        .drag-overlay.visible {
            display: flex;
        }

        .drag-placeholder {
            background: rgba(99, 102, 241, 0.3);
            border: 2px dashed #6366f1;
            border-radius: 8px;
            padding: 20px;
            font-size: 18px;
            color: #6366f1;
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">
<!-- 拖放覆盖层 -->
<div class="drag-overlay" id="drag-overlay">
    <div class="drag-placeholder">Drag and drop blocks here</div>
</div>

<!-- 顶部工具栏 -->
<header class="fixed top-0 left-0 right-0 z-50 bg-white shadow-md">
    <div class="mx-auto px-4 py-2 flex items-center justify-between">
        <!-- 左侧：Logo -->
        <div class="flex items-center">
            <div class="bg-primary w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-magic text-white"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-800">Page Builder</h1>
        </div>

        <!-- 中间：设备预览 -->
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

        <!-- 右侧：操作按钮 -->
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
        <div id="editor-canvas" class="!bg-none !bg-white  mb-4 px-8 pt-8 border-slate-300 rounded-lg">
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

        <!-- 配置面板内容 -->
        <div id="config-content" class=" config-panel">
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

<!-- 模态框 -->
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


<script src="https://cdn.jsdelivr.net/npm/grapesjs/dist/grapes.min.js"></script>
<script>
    const data = @json($page['content']);
    // init
    const editor = grapesjs.init({
        container: '#editor-canvas',
        fromElement: false,
        height: '100%',
        width: 'auto',
        storageManager: false,
        canvas: {
            styles: @json(themed_styles())
        },
        blockManager: {
            appendTo: '#blocks',
        },
        panels: {
            defaults: [
                {
                    id: 'blocks',
                    el: '#blocks',
                },
            ],
        },
        deviceManager: {
            devices: [
                {
                    id: 'desktop',
                    name: 'Desktop',
                    width: '',
                },
                {
                    id: 'tablet',
                    name: 'Tablet',
                    width: '768px',
                    widthMedia: '992px',
                },
                {
                    id: 'mobile',
                    name: 'Mobile',
                    width: '320px',
                    widthMedia: '768px',
                }
            ]
        }
    });

    const defaultHtml = `
    <h1 class="text-3xl mt-5 text-center">Hello, welcome to Page Builder<br/>Start dragging components from the right!</h1>
`;

    fetch('{{ admin_route('api.pages.block-registry') }}')
        .then(res => res.json())
        .then(result => {
            const categories = result.data;
            registerBlocks(editor, categories);
            render(data)
        });

    function render(pageData) {
        if (pageData) {
            editor.loadProjectData(JSON.parse(pageData));
            updateBlockCount();
        } else {
            editor.addComponents(defaultHtml);
        }
    }

    editor.on('load', () => {
        editor.on('component:remove', updateBlockCount);

        // Add event listener for component add to generate unique references
        editor.on('component:add', (component) => {
            ensureComponentReference(component);
            updateBlockCount();
        });

        // Add event listener for component update to ensure reference exists
        editor.on('component:update', (component) => {
            ensureComponentReference(component);
        });
    });

    function registerBlocks(editor, categories) {
        const blockManager = editor.BlockManager;
        const domComponents = editor.DomComponents;

        categories.forEach(category => {
            const categoryId = category.title;
            const categoryLabel = category.label || category.title;

            category.blocks
                .sort((a, b) => a.sort - b.sort)
                .forEach(comp => {
                    const {
                        id,
                        tagName = 'div',
                        content = '<div></div>',
                        label,
                        description = '',
                        sort = 0,
                        droppable = false,
                    } = comp;

                    domComponents.addType(label, {
                        model: {
                            defaults: {
                                tagName,
                                content,
                            },
                        },
                    });

                    // Add block to block manager
                    blockManager.add(label, {
                        label: `
                        <div class="flex items-start text-left">
                            <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mr-3"></div>
                            <div class="flex-1">
                                <strong class="text-base">${label}</strong>
                                <div class="text-sm text-gray-500">${description}</div>
                            </div>
                        </div>
                    `,
                        category: {
                            id: categoryId,
                            label: `${categoryLabel}`,
                        },
                        content: {
                            type: label,
                            block_id: id,
                            droppable
                        },
                        order: sort,
                    });
                });
        });
    }

    function generateRandomId(type) {
        const rand = crypto.getRandomValues(new Uint32Array(1))[0].toString(36);
        return `${type.toLowerCase()}-${rand}`;
    }

    function ensureComponentReference(component) {
        if (!component.get('reference')) {
            component.set('reference', generateRandomId(component.get('type')));
        }
    }

    // ================ Panel ================
    document.querySelectorAll('.device-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.device-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            editor.setDevice(this.dataset.device);
        });
    });

    document.getElementById('undo-btn').addEventListener('click', () => {
        editor.UndoManager.undo();
    });

    document.getElementById('redo-btn').addEventListener('click', () => {
        editor.UndoManager.redo();
    });

    document.getElementById('reset-btn').addEventListener('click', () => {
        if (confirm('Are you sure you want to reset the page content? This cannot be undone.')) {
            editor.DomComponents.clear();
            editor.addComponents(defaultHtml);
            fetch(`{{ admin_route('api.pages.reset',[$reference]) }}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
                .then(res => {
                    if (!res.ok) throw new Error('Failed to reset');
                    return res.json();
                })
                .then(data => {
                    showNotification(data.message || 'Page content has been reset.', true);
                })
                .catch(err => {
                    console.error('Reset failed:', err);
                    showNotification('Reset failed ❌', false);
                });
        }
    });

    document.getElementById('save-config-btn').addEventListener('click', () => {
        fetch('{{ admin_route('api.pages.data',[$reference]) }}')
            .then(res => res.json())
            .then(result => {
                const content = result.data.page.content;
                render(content)
            });
    });

    document.getElementById('save-btn').addEventListener('click', () => {
        const projectData = editor.getProjectData();

        // Ensure all components have unique references
        const components = editor.getComponents();
        components.forEach(component => {
            ensureComponentReference(component);
        });

        fetch(`{{ admin_route('api.pages.save',[$reference]) }}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // if using Laravel
            },
            body: JSON.stringify({
                data: projectData
            })
        })
            .then(res => {
                if (!res.ok) throw new Error('Failed to save');
                return res.json();
            })
            .then(data => {
                showNotification(data.message || 'Saved successfully ✅', true);
            })
            .catch(err => {
                console.error('Save failed:', err);
                showNotification('Save failed ❌', false);
            });
    });

    // Preview
    const previewBtn = document.getElementById('preview-btn');
    const closeBtn = document.getElementById('close-preview');
    const previewModal = document.getElementById('preview-modal');
    const previewContent = document.getElementById('preview-content'); // 假设你有这个区域包住内容

    // preview
    previewBtn.addEventListener('click', () => {
        const html = editor.getHtml();
        const css = editor.getCss();

        previewContent.innerHTML = `
        <style>${css}</style>
        <div class="max-w-full mx-auto p-5">${html}</div>
    `;

        previewModal.classList.remove('hidden');
    });

    // close
    closeBtn.addEventListener('click', () => {
        previewModal.classList.add('hidden');
    });

    // close when clicking on background
    previewModal.addEventListener('click', (e) => {
        if (e.target === previewModal) {
            previewModal.classList.add('hidden');
        }
    });

    // esc to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !previewModal.classList.contains('hidden')) {
            previewModal.classList.add('hidden');
        }
    });


    function updateBlockCount() {
        const wrapper = editor.getWrapper();
        if (!wrapper || typeof wrapper.components !== 'function') {
            //console.warn('Editor wrapper not ready or has no components()');
            return;
        }
        const components = wrapper.components();
        document.getElementById('block-count').textContent = components.length;
    }

    // tab
    const blocksTab = document.getElementById('blocks-tab');
    const configTab = document.getElementById('config-tab');
    const blocksContent = document.getElementById('blocks-content');
    const configContent = document.getElementById('config-content');

    blocksTab.addEventListener('click', () => {
        blocksTab.classList.add('active');
        configTab.classList.remove('active');
        blocksContent.classList.remove('hidden');
        configContent.classList.add('hidden');
    });

    configTab.addEventListener('click', () => {
        configTab.classList.add('active');
        blocksTab.classList.remove('active');
        configContent.classList.remove('hidden');
        blocksContent.classList.add('hidden');
    });

    function showNotification(message, isSuccess) {
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.className = `fixed top-20 right-4 px-4 py-2 rounded-md shadow-lg text-white font-medium z-50 ${
            isSuccess ? 'bg-green-500' : 'bg-red-500'
        }`;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>
</body>
</html>
