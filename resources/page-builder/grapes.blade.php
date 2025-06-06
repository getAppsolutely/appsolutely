<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Builder</title>
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
                    }
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
            margin-bottom: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: white;
        }

        .gjs-block:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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
            <div class="text-sm text-slate-500">
                <i class="fas fa-cube mr-1 mt-1"></i><span id="block-count">0</span> blocks
            </div>
            <button id="undo-btn"
                    class="flex items-center bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-md text-sm">
                <i class="fas fa-undo mr-2"></i>Undo
            </button>
            <button id="redo-btn"
                    class="flex items-center bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-md text-sm">
                <i class="fas fa-redo mr-2"></i>Redo
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

<main class="pt-16 mx-auto flex grapes-editor">
    <section class="flex-1 shadow-lg rounded-lg mr-4 px-6 editor-canvas-wrapper">
        <div id="editor-canvas" class="border-slate-300 rounded-lg mb-4">
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

            <div class="relative mb-4">
                <input type="text" placeholder="Search blocks..."
                       class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                <i class="fas fa-search absolute right-3 top-3 text-slate-400"></i>
            </div>

            <div class="gjs-blocks-c space-y-6" id="blocks">
            </div>
        </div>

        <!-- 配置面板内容 -->
        <div id="config-content" class="hidden config-panel">
            <h2 class="text-lg font-semibold mb-4 text-slate-700">
                <i class="fas fa-cog mr-2 text-primary"></i>Config Panel
            </h2>

            <div id="config-form" class="space-y-6">
                <div class="text-center py-10 text-slate-400">
                    <i class="fas fa-mouse-pointer text-4xl mb-3"></i>
                    <p>请选择一个组件进行配置</p>
                </div>

                <!-- Hero配置表单 (默认隐藏) -->
                <div id="hero-config" class="hidden space-y-4">
                    <div>
                        <h3 class="font-medium text-slate-700 mb-2">主横幅配置</h3>
                        <p class="text-sm text-slate-500 mb-4">设置吸引人的顶部横幅区域</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">标题</label>
                        <input type="text" class="w-full px-3 py-2 border border-slate-300 rounded-md"
                               placeholder="输入主标题" value="欢迎来到我们的平台">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">副标题</label>
                        <input type="text" class="w-full px-3 py-2 border border-slate-300 rounded-md"
                               placeholder="输入副标题" value="发现我们的优质产品和服务">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">背景图片</label>
                        <div class="flex items-center space-x-3">
                            <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16"></div>
                            <button class="px-3 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-sm">
                                上传图片
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">按钮文字</label>
                            <input type="text" class="w-full px-3 py-2 border border-slate-300 rounded-md"
                                   placeholder="例如：了解更多" value="开始探索">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">按钮链接</label>
                            <input type="text" class="w-full px-3 py-2 border border-slate-300 rounded-md"
                                   placeholder="输入URL" value="/products">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button id="add-hero-btn"
                                class="w-full bg-primary hover:bg-indigo-600 text-white py-2 rounded-md font-medium">
                            添加到页面
                        </button>
                    </div>
                </div>

                <!-- 特性展示配置表单 (默认隐藏) -->
                <div id="feature-config" class="hidden space-y-4">
                    <div>
                        <h3 class="font-medium text-slate-700 mb-2">特性展示配置</h3>
                        <p class="text-sm text-slate-500 mb-4">展示产品特性或服务优势</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">主标题</label>
                        <input type="text" class="w-full px-3 py-2 border border-slate-300 rounded-md"
                               placeholder="输入主标题" value="我们的核心优势">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">描述</label>
                        <textarea class="w-full px-3 py-2 border border-slate-300 rounded-md" placeholder="输入描述内容"
                                  rows="3">我们提供业界领先的解决方案，满足您的所有需求</textarea>
                    </div>

                    <div class="pt-4">
                        <button id="add-feature-btn"
                                class="w-full bg-primary hover:bg-indigo-600 text-white py-2 rounded-md font-medium">
                            添加到页面
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</main>

<!-- 模态框 -->
<div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-75 items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg max-w-full overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Preview</h3>
            <button id="close-preview" class="text-slate-500 hover:text-slate-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="h-full p-4 overflow-auto" id="preview-content">
            <!-- 预览内容将在这里显示 -->
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/grapesjs/dist/grapes.min.js"></script>
<script>
    // 初始化GrapesJS编辑器
    const editor = grapesjs.init({
        container: '#editor-canvas',
        fromElement: false,
        height: '100%',
        width: 'auto',
        storageManager: false,
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
            devices: [{
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

    editor.on('load', () => {
        const iframe = editor.Canvas.getFrameEl();
        const head = iframe.contentDocument.head;
        const style = document.createElement('link');
        style.rel = 'stylesheet';
        style.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css';
        head.appendChild(style);
    });

    fetch('/dash/api/pages/blocks/registry')
        .then(res => res.json())
        .then(result => {
            const categories = result.data;
            registerBlocks(editor, categories);
        });

    function registerBlocks(editor, categories) {
        const blockManager = editor.BlockManager;
        const domComponents = editor.DomComponents;

        categories.forEach(category => {
            const categoryId = category.name;
            const categoryLabel = category.label || category.name;

            category.blocks
                .sort((a, b) => a.sort - b.sort)
                .forEach(comp => {
                    const {
                        type,
                        tagName = 'div',
                        style = {},
                        content = '<div></div>',
                        label = type,
                        desc = '',
                        sort = 0,
                        droppable = false,
                    } = comp;
                    console.log(comp)

                    // Register reusable component type (optional if you're using `type`)
                    domComponents.addType(type, {
                        model: {
                            defaults: {
                                tagName,
                                style,
                                content,
                            },
                        },
                    });

                    // Add block to block manager
                    blockManager.add(type, {
                        label: `
                        <div class="flex items-start text-left">
                            <div class="bg-gray-200 border-2 border-dashed rounded-xl w-10 h-10 mr-3"></div>
                            <div class="flex-1">
                                <strong>${label}</strong>
                                <div class="text-xs text-gray-500">${desc}</div>
                            </div>
                        </div>
                    `,
                        category: {
                            id: categoryId,
                            label: `${categoryLabel}`,
                        },
                        content: {type, droppable},
                        attributes: {class: 'gjs-block'},
                        order: sort,
                    });
                });
        });
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

    document.getElementById('save-btn').addEventListener('click', () => {
        const projectData = editor.getProjectData();
        console.log('Data:', projectData);

        showNotification('Data saved', true);
    });

    // Preview
    const previewModal = document.getElementById('preview-modal');
    const previewContent = document.getElementById('preview-content');

    document.getElementById('preview-btn').addEventListener('click', () => {
        const html = editor.getHtml();
        const css = editor.getCss();
        previewContent.innerHTML = `
                <style>${css}</style>
                <div class="max-w-full mx-auto p-5">${html}</div>
            `;
        previewModal.classList.remove('hidden');
    });

    document.getElementById('close-preview').addEventListener('click', () => {
        previewModal.classList.add('hidden');
    });

    // 组件计数更新
    editor.on('component:add', () => {
        updateBlockCount();
    });

    editor.on('component:remove', () => {
        updateBlockCount();
    });

    function updateBlockCount() {
        const block = editor.getComponents();
        document.getElementById('block-count').textContent = block.length;
    }

    updateBlockCount();

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

    // init
    setTimeout(() => {
        editor.addComponents(`
                <div class="text-center">
                    <h1 class="">Hello, welcome to Page Builder</h1>
                </div>
            `);
    }, 500);
</script>
</body>
</html>
