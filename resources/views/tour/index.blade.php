<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>360 Virtual Tour</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js"></script>
    <script src="https://code.responsivevoice.org/responsivevoice.js?key=QlpaIuG0"></script>

    <!-- Tailwind CSS v4 -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        @theme {
            --color-primary: #6366f1;
            --color-accent: #f43f5e;
            --color-bg-glass: rgba(15, 23, 42, 0.95);
            --color-border-glass: rgba(255, 255, 255, 0.12);
            --font-outfit: 'Outfit', sans-serif;
            --animate-fade-in: fadeIn 0.4s ease-out;
            --animate-spin-slow: spin 1s infinite linear;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Base element tweaks */
        model-viewer {
            width: 100%;
            height: 100%;
            --progress-bar-color: var(--color-primary);
        }

        /* Utilities */
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* =============================================
           VANILLA CAROUSEL — Asset Media Slider
           ============================================= */
        .vc-wrap {
            position: relative;
            width: 100%;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            overflow: hidden;
            /* hides sliding track; zoom handled via img transform */
        }

        .vc-track {
            display: flex;
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            will-change: transform;
        }

        .vc-slide {
            flex: 0 0 auto;
            position: relative;
            flex-shrink: 0;
        }

        .vc-slide img {
            width: 100%;
            height: 300px;
            object-fit: contain;
            display: block;
            cursor: zoom-in;
            transform-origin: 50% 50%;
            /* overridden dynamically on wheel */
            transition: transform 0.15s ease;
            user-select: none;
            background: rgba(0, 0, 0, 0.2);
        }

        .vc-slide .mv-wrap {
            width: 100%;
            height: 300px;
        }

        .vc-slide .mv-wrap model-viewer {
            width: 100% !important;
            height: 100% !important;
        }

        .vc-slide-label {
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.4);
            text-align: center;
            padding: 8px;
            background: rgba(0, 0, 0, 0.35);
        }

        /* Nav arrows */
        .vc-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(10, 15, 30, 0.75);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            backdrop-filter: blur(10px);
            transition: background 0.2s;
            z-index: 10;
        }

        .vc-btn:hover {
            background: rgba(99, 102, 241, 0.75);
        }

        .vc-btn.vc-prev {
            left: 10px;
        }

        .vc-btn.vc-next {
            right: 10px;
        }

        .vc-btn:disabled {
            opacity: 0.2;
            cursor: default;
        }

        /* Dots — inside card, absolute bottom center */
        .vc-dots {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            gap: 6px;
            z-index: 10;
            pointer-events: auto;
        }

        .vc-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            padding: 0;
        }

        .vc-dot.active {
            background: #6366f1;
            transform: scale(1.5);
        }

        /* Product Tabs */
        .product-tabs-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            overflow-x: auto;
            padding: 12px 4px 4px 4px;
            scrollbar-width: none;
        }

        .product-tabs-container::-webkit-scrollbar {
            display: none;
        }

        .product-tab {
            padding: 6px 12px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid transparent;
            color: rgba(255, 255, 255, 0.6);
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-tab:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .product-tab.active {
            background: rgba(99, 102, 241, 0.2);
            border-color: rgba(99, 102, 241, 0.5);
            color: #fff;
        }

        /* Type badge */
        .vc-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 6px;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            z-index: 5;
        }

        .vc-badge-2d {
            background: rgba(37, 99, 235, 0.65);
            color: #bfdbfe;
        }

        .vc-badge-3d {
            background: rgba(124, 58, 237, 0.65);
            color: #ddd6fe;
        }

        /* =============================================
           SCENE LIST — Sidebar Thumbnails
           ============================================= */
        #scene-list-panel {
            scrollbar-width: none;
        }

        #scene-list-panel::-webkit-scrollbar {
            display: none;
        }

        .scene-card {
            position: relative;
            width: 100px;
            height: 65px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(0, 0, 0, 0.5);
            flex-shrink: 0;
        }

        /* Mobile adjustment for scene-card */
        @media (max-width: 640px) {
            .scene-card {
                width: 80px;
                height: 55px;
            }
        }

        .scene-card:hover {
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
        }

        .scene-card.active {
            border-color: #6366f1;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.6);
        }

        .scene-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .scene-card:hover img {
            transform: scale(1.1);
        }

        .scene-card-label {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 6px 8px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
            color: white;
            font-size: 8px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* HD Loader */
        /* Bottom Right Controls Group */
        .bottom-right-controls {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 10001;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-direction: row-reverse;
            pointer-events: none;
        }

        .bottom-right-controls>* {
            pointer-events: auto;
        }

        .hd-loader {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            color: white;
            padding: 8px 16px;
            border-radius: 100px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            display: none;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            opacity: 0;
            transition: opacity 0.5s ease;
            white-space: nowrap;
        }

        .hd-loader.visible {
            display: flex;
            opacity: 1;
        }

        /* Resolution Selector */
        .res-selector {
            position: relative;
        }

        .res-btn {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .res-btn:hover {
            background: rgba(15, 23, 42, 0.8);
        }

        .res-menu {
            position: absolute;
            bottom: calc(100% + 10px);
            right: 0;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            display: none;
            flex-direction: column;
            min-width: 140px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .res-menu.show {
            display: flex;
        }

        .res-menu button {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            padding: 12px 16px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .res-menu button:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .res-menu button.active {
            color: #6366f1;
            background: rgba(99, 102, 241, 0.1);
        }

        .hd-loader.visible {
            opacity: 1;
        }

        .hd-loader .spinner {
            width: 12px;
            height: 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: hd-spin 0.8s linear infinite;
        }

        @keyframes hd-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="m-0 p-0 w-full h-full overflow-hidden bg-black font-outfit">

    <div id="loader"
        class="fixed top-0 left-0 w-full h-full bg-[#0f172a] flex flex-col items-center justify-center z-[50000] text-white transition-opacity duration-1000">
        <div
            class="w-[50px] h-[50px] border-[5px] border-white/5 border-t-primary rounded-full animate-spin-slow mb-[25px]">
        </div>
        <div class="font-[300] tracking-[6px] text-xs">CLEANING UI...</div>
    </div>

    <!-- Modal System -->
    <div id="modal"
        class="group fixed top-0 left-0 w-full h-full bg-black/60 backdrop-blur-[15px] flex items-center justify-center z-[20000] opacity-0 invisible transition-all duration-400 [&.active]:opacity-100 [&.active]:visible">
        <div
            class="bg-bg-glass border border-border-glass py-5 px-6 md:py-[20px] md:px-[25px] rounded-[25px] max-w-[1000px] w-[90%] max-h-[85vh] flex flex-col text-white transform scale-80 transition-transform duration-400 text-left relative scrollbar-none group-[.active]:scale-100 overflow-hidden">

            <div class="flex items-center justify-between mb-[15px]">
                <h2 id="modal-title" class="m-0 text-2xl">Info</h2>
                <div class="text-[1.2rem] text-white/40 cursor-pointer transition-all duration-300 z-10 w-[36px] h-[36px] flex items-center justify-center rounded-full bg-white/10 shrink-0 hover:text-white hover:bg-accent hover:rotate-90"
                    onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <!-- Wrapper Layout -->
            <div class="flex flex-col gap-5 md:flex-row md:gap-[30px] md:items-start" id="modal-layout-wrapper">

                <!-- Asset Carousel Pane -->
                <div class="flex-1 w-full hidden" id="modal-pane-assets">

                    <!-- track wrapper -->
                    <div class="vc-wrap" id="vc-wrap">
                        <div class="vc-track" id="vc-track"></div>
                        <button class="vc-btn vc-prev" id="vc-prev" aria-label="Previous"><i
                                class="fas fa-chevron-left"></i></button>
                        <button class="vc-btn vc-next" id="vc-next" aria-label="Next"><i
                                class="fas fa-chevron-right"></i></button>

                        <!-- Enlarge Button -->
                        <button
                            class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-black/50 backdrop-blur-md border border-white/20 text-white flex items-center justify-center cursor-pointer z-20 hover:bg-primary transition-all"
                            onclick="toggleEnlarge()" title="Enlarge View">
                            <i class="fas fa-expand-arrows-alt text-xs" id="enlarge-icon"></i>
                        </button>

                        <div class="vc-dots" id="vc-dots"></div>
                    </div>

                    <!-- Product Tabs -->
                    <div id="product-tabs" class="product-tabs-container hidden"></div>
                </div>

                <!-- Text Pane -->
                <div class="flex-1 w-full" id="modal-pane-text">
                    <div class="flex flex-col w-full">
                        <div class="flex items-center gap-3 mb-[15px] border-b border-white/10 pb-[10px]">
                            <!-- ID Group -->
                            <div class="flex items-center h-8 bg-white/5 border border-white/10 rounded-md overflow-hidden transition-all duration-300 [&.active]:bg-primary/20 [&.active]:border-primary/50"
                                id="tab-id-container">
                                <button id="btn-tab-id"
                                    class="tab-btn active py-0 px-3 h-full flex items-center justify-center cursor-pointer hover:bg-white/10 [&.active]:bg-primary/30"
                                    onclick="switchTab('id')">
                                    <img src="https://flagcdn.com/w40/id.png"
                                        class="w-5 h-3.5 object-cover rounded-[1px] shadow-sm" alt="ID">
                                </button>
                            </div>

                            <!-- EN Group -->
                            <div class="flex items-center h-8 bg-white/5 border border-white/10 rounded-md overflow-hidden transition-all duration-300 [&.active]:bg-primary/20 [&.active]:border-primary/50"
                                id="tab-en-container">
                                <button id="btn-tab-en"
                                    class="tab-btn py-0 px-3 h-full flex items-center justify-center cursor-pointer hover:bg-white/10 [&.active]:bg-primary/30"
                                    onclick="switchTab('en')">
                                    <img src="https://flagcdn.com/w40/gb.png"
                                        class="w-5 h-3.5 object-cover rounded-[1px] shadow-sm" alt="EN">
                                </button>
                            </div>

                            <div class="grow"></div>

                            <!-- Extra Tabs (Researcher & Contact) -->
                            <div id="extra-tabs-wrapper" class="flex items-center gap-2 pr-3 hidden">
                                <button id="btn-tab-researcher"
                                    class="px-3 py-2 rounded-md bg-white/5 border border-white/10 text-[9px] font-bold text-slate-400 uppercase tracking-widest hover:bg-white/10 transition-all [&.active]:bg-emerald-500/20 [&.active]:border-emerald-500/50 [&.active]:text-emerald-400"
                                    onclick="switchTab('researcher')">
                                    <i class="fas fa-user-tie mr-1"></i> Researcher
                                </button>
                                <button id="btn-tab-contact"
                                    class="px-3 py-2 rounded-md bg-white/5 border border-white/10 text-[9px] font-bold text-slate-400 uppercase tracking-widest hover:bg-white/10 transition-all [&.active]:bg-sky-500/20 [&.active]:border-sky-500/50 [&.active]:text-sky-400"
                                    onclick="switchTab('contact')">
                                    <i class="fas fa-address-book mr-1"></i> Contact
                                </button>
                            </div>

                            <!-- VO Controls Template -->
                            <div id="vo-controls-wrapper" class="flex items-center h-full animate-fade-in pl-1">
                                <button id="btn-play"
                                    class="py-1.5 px-3 flex items-center justify-center text-primary text-[10px] cursor-pointer hover:bg-primary/20 transition-all h-full"
                                    onclick="playNarration()"><i class="fas fa-play"></i></button>
                                <button id="btn-stop"
                                    class="py-1.5 px-3 flex items-center justify-center text-rose-500 text-[10px] cursor-pointer hidden hover:bg-rose-500/20 transition-all h-full"
                                    onclick="stopNarration()"><i class="fas fa-stop"></i></button>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto max-h-[250px] md:max-h-[400px] pr-2 scrollbar-none">
                            <div class="leading-[1.6] text-white/80 text-justify hidden opacity-0 [&.active]:block [&.active]:animate-fade-in [&.active]:opacity-100 [&_p]:mt-0 [&_p]:mb-[1em]"
                                id="tab-id"></div>
                            <div class="leading-[1.6] text-white/80 text-justify hidden opacity-0 [&.active]:block [&.active]:animate-fade-in [&.active]:opacity-100 [&_p]:mt-0 [&_p]:mb-[1em]"
                                id="tab-en"></div>
                            <div class="leading-[1.6] text-white/80 hidden opacity-0 [&.active]:block [&.active]:animate-fade-in [&.active]:opacity-100 [&_p]:mt-0 [&_p]:mb-[1em]"
                                id="tab-researcher"></div>
                            <div class="leading-[1.6] text-white/80 hidden opacity-0 [&.active]:block [&.active]:animate-fade-in [&.active]:opacity-100 [&_p]:mt-0 [&_p]:mb-[1em] font-mono"
                                id="tab-contact"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- 3D Canvas -->
    <div id="viewer-container" class="w-full h-screen bg-black">
        <!-- Bottom Right UI Controls -->
        <div class="bottom-right-controls">
            <!-- Resolution Selector -->
            <div class="res-selector" id="res-selector">
                <button class="res-btn" id="active-res-btn">
                    <i class="fas fa-signal"></i>
                    <span id="current-res-label">AUTO</span>
                </button>
                <div class="res-menu" id="res-menu">
                    <button data-res="low">SD (15%)</button>
                    <button data-res="medium">HD (40%)</button>
                    <button data-res="high" class="active">ULTRA (AUTO)</button>
                </div>
            </div>

            <!-- HD Loader -->
            <div id="hd-loader" class="hd-loader">
                <div class="spinner"></div>
                <span>UPGRADING QUALITY...</span>
            </div>
        </div>
    </div>

    <!-- UI Overlay -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none p-[15px] box-border z-[10000]">

        <div class="flex items-start justify-between w-full" id="overlay-top-wrapper">
            <!-- Left Group: Vertical Container -->
            <div class="flex flex-col items-start gap-[12px] pointer-events-auto" id="left-sidebar-container">
                <!-- Top Group: Responsive Toggle + Controls -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-[12px]" id="top-control-bar">
                    <!-- UI Toggle Button (Always Visible) -->
                    <div id="ui-toggle"
                        class="w-[48px] h-[48px] bg-bg-glass backdrop-blur-[25px] border border-border-glass rounded-lg text-white flex items-center justify-center cursor-pointer transition-all duration-400 ease-[cubic-bezier(0.175,0.885,0.32,1.275)] shadow-[0_15px_30px_rgba(0,0,0,0.4)] hover:bg-primary hover:scale-110 hover:rotate-6 text-[1.1rem]">
                        <i class="fas fa-times"></i>
                    </div>

                    <!-- Control Buttons (Toggleable) -->
                    <div id="control-buttons-panel"
                        class="bg-bg-glass backdrop-blur-[30px] border border-border-glass p-[6px] rounded-lg flex sm:flex-row flex-col gap-[6px] shadow-[0_20px_40px_rgba(0,0,0,0.4)] transition-all duration-500 ease-in-out [&.minimized]:opacity-0 [&.minimized]:-translate-x-[20px] sm:[&.minimized]:-translate-x-[20px] [&.minimized]:-translate-y-[10px] [&.minimized]:pointer-events-none [&.minimized]:blur-[10px]">
                        <button id="toggle-rotate"
                            class="btn-action btn-active bg-white/5 hover:bg-white/15 [&.btn-active]:bg-primary/35 border border-border-glass [&.btn-active]:border-primary/80 text-white/90 w-[38px] h-[38px] rounded-[12px] cursor-pointer inline-flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95"
                            title="Auto Rotation"><i class="fas fa-sync-alt text-[14px] text-primary"></i></button>

                        <button id="toggle-markers"
                            class="btn-action btn-active bg-white/5 hover:bg-white/15 [&.btn-active]:bg-primary/35 border border-border-glass [&.btn-active]:border-primary/80 text-white/90 w-[38px] h-[38px] rounded-[12px] cursor-pointer inline-flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95"
                            title="Show Markers"><i class="bi bi-eye-fill text-[14px] text-primary"></i></button>

                        <button id="toggle-map"
                            class="btn-action bg-white/5 hover:bg-white/15 border border-border-glass text-white/90 w-[38px] h-[38px] rounded-[12px] cursor-pointer inline-flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95"
                            title="Site Plan"><i class="fas fa-map text-[14px] text-primary"></i></button>

                        <button id="toggle-fullscreen"
                            class="btn-action bg-white/5 hover:bg-white/15 [&.btn-active]:bg-primary/35 border border-border-glass [&.btn-active]:border-primary/80 text-white/90 w-[38px] h-[38px] rounded-[12px] cursor-pointer inline-flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95"
                            title="Full Screen"><i class="fas fa-expand text-[14px] text-primary"></i></button>
                    </div>
                </div>

                <!-- Scene List Sidebar (Toggleable, Below controls) -->
                <div id="scene-list-panel"
                    class="flex flex-col gap-[6px] max-h-[calc(100vh-250px)] overflow-y-auto scrollbar-none transition-all duration-500 ease-in-out [&.minimized]:opacity-0 [&.minimized]:-translate-x-[40px] [&.minimized]:pointer-events-none [&.minimized]:blur-[10px]">
                    <!-- Items dynamic -->
                </div>
            </div>

            <!-- Right Panel: Scene Title (Always Visible) -->
            <div
                class="bg-bg-glass backdrop-blur-[30px] border border-border-glass p-[10px_22px] rounded-lg pointer-events-auto shadow-[0_30px_60px_rgba(0,0,0,0.5)] origin-right text-right">
                @php
                    $startScene = $tour->scenes->where('is_start_scene', true)->first() ?? $tour->scenes->first();
                @endphp
                <h1 id="scene-title" class="m-0 text-[16px] font-bold text-white tracking-[0.8px]">
                    {{ $startScene->name ?? 'SCENE' }}</h1>
                <p id="scene-subtitle"
                    class="mt-[4px] m-0 text-[9px] text-white/50 font-bold tracking-[2px] uppercase">
                    {{ strtoupper($tour->name) }}</p>
            </div>
        </div>

        <!-- Bottom Section (Flex empty space helper) -->
        <div class="flex-1"></div>

        <!-- Coord Pill -->
        <div class="bg-bg-glass backdrop-blur-[20px] border border-border-glass px-[25px] py-[10px] rounded-[50px] text-white font-mono text-[13px] self-start pointer-events-auto opacity-0 invisible transition-all duration-400 shadow-[0_10px_30px_rgba(0,0,0,0.5)] [&.show]:opacity-100 [&.show]:visible"
            id="coord-display">Ambil kordinat dengan klik ruangan...</div>
    </div>

    <!-- Site Plan Overlay -->
    <div id="site-plan-overlay"
        class="fixed top-0 left-0 w-full h-full bg-black/80 backdrop-blur-xl z-[25000] opacity-0 invisible transition-all duration-500 flex items-center justify-center p-6 md:p-12">
        <div
            class="relative bg-[#0f172a]/90 border border-white/10 py-5 px-6 md:py-[20px] md:px-[25px] rounded-[25px] max-w-[1000px] w-[90%] max-h-[85vh] flex flex-col items-center shadow-2xl">
            <!-- Header -->
            <div class="w-full flex items-center justify-between mb-[15px]">
                <div>
                    <h2 class="text-white text-xl font-black uppercase tracking-tighter">Site Plan & Maps</h2>
                    <p class="text-white/40 text-[9px] font-bold uppercase tracking-[2px] mt-1">Select a location to
                        navigate</p>
                </div>
                <button
                    class="w-10 h-10 bg-white/10 rounded-full text-white/60 hover:text-white hover:bg-rose-500 transition-all flex items-center justify-center cursor-pointer"
                    onclick="closeSitePlan()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Map Canvas -->
            <div class="flex-1 w-full relative overflow-hidden flex items-center justify-center rounded-[24px] bg-black/20 border border-white/5 p-4"
                id="map-canvas-area">
                <div id="active-map-container"
                    class="relative inline-block transition-transform duration-200 ease-out origin-center cursor-grab active:cursor-grabbing">
                    <!-- Map Image will be injected here -->
                </div>
            </div>

            <!-- Map Selector -->
            @if ($tour->sitePlans->count() > 1)
                <div class="w-full flex items-center justify-center gap-4 mt-6 overflow-x-auto py-2 scrollbar-none">
                    @foreach ($tour->sitePlans as $plan)
                        <button
                            class="px-6 py-2 bg-white/5 border border-white/10 rounded-full text-white/60 text-[10px] font-bold uppercase tracking-widest hover:bg-primary/20 hover:text-white hover:border-primary/50 transition-all whitespace-nowrap cursor-pointer plan-tab-btn"
                            data-id="{{ $plan->id }}" onclick="loadMap({{ $plan->id }})">
                            {{ $plan->name }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script src="https://pchen66.github.io/js/three/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.105.0/examples/js/loaders/GLTFLoader.js"></script>
    <script src="https://pchen66.github.io/js/panolens/panolens.min.js"></script>

    <script>
        const container = document.querySelector('#viewer-container');
        const loader = document.getElementById('loader');
        let viewer; // Global viewer instance
        let infoUrl, arrowUrl, threedUrl; // Icon URLs
        const tourData = {!! $tour->toJson() !!};
        const panoramas = {};
        const mixers = [];
        const clock = new THREE.Clock();
        const loader3d = new THREE.GLTFLoader();
        const UNIFORM_SIZE = 500;
        let currentSceneData = null;

        async function createStyledIcon(iconString, color = '#6366f1', rotation = 0) {
            await document.fonts.ready;
            const canvas = document.createElement('canvas');
            canvas.width = 150;
            canvas.height = 150;
            const ctx = canvas.getContext('2d');

            if (rotation) {
                ctx.translate(75, 75);
                ctx.rotate(rotation * Math.PI / 180);
                ctx.translate(-75, -75);
            }
            ctx.beginPath();
            ctx.arc(75, 75, 60, 0, 2 * Math.PI);
            ctx.fillStyle = color;
            ctx.fill();
            ctx.fillStyle = "white";
            ctx.font = 'bold 80px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(iconString, 75, 80);
            return canvas.toDataURL();
        }

        function animate3d() {
            requestAnimationFrame(animate3d);
            if (clock) {
                const delta = clock.getDelta();
                mixers.forEach(mixer => mixer.update(delta));
            }

            // Only process current panorama to save CPU/Battery on mobile
            if (typeof viewer !== 'undefined' && viewer.panorama) {
                viewer.panorama.children.forEach(child => {
                    // Sync 3D model positions
                    if (child.is3DModel && child.modelObj && child.syncPosition) {
                        child.modelObj.position.copy(child.position);
                    }

                    // Auto-rotate 3D items
                    if ((child.is3DModel || child.isPerspectiveMesh) && !child.isNavMarker && !child
                        .isBeingDragged) {
                        child.rotation.y += 0.005;
                    }
                });
            }
        }
        animate3d(); // Start the loop

        async function loadGLB(url, spotData) {
            return new Promise((resolve, reject) => {
                loader3d.load(url, (gltf) => {
                    const model = gltf.scene;

                    // Scale up significantly for world-space visibility
                    const s = 1000; // Increased to 1000 for maximum visibility
                    model.position.set(0, 0, 0); // Position is handled by proxy
                    model.rotation.set(spotData.rotation_x || 0, spotData.rotation_y || 0, spotData
                        .rotation_z || 0);
                    model.scale.set(
                        (spotData.scale_x || 1) * s,
                        (spotData.scale_y || 1) * s,
                        (spotData.scale_z || spotData.scale_x || 1) * s
                    );

                    model.is3DModel = true;

                    // Set maximum renderOrder and disable depthTest for "always on top" visibility
                    model.traverse(node => {
                        if (node.isMesh) {
                            node.renderOrder = 9999;
                            if (node.material) {
                                node.material.depthTest = false;
                                node.material.depthWrite = false;
                                node.material.transparent = true;
                            }
                        }
                    });

                    if (gltf.animations && gltf.animations.length > 0) {
                        const mixer = new THREE.AnimationMixer(model);
                        gltf.animations.forEach(clip => mixer.clipAction(clip).play());
                        mixers.push(mixer);
                    }

                    resolve(model);
                }, undefined, reject);
            });
        }

        function addBounce(infospot) {
            const startY = infospot.position.y;
            const tween = new TWEEN.Tween(infospot.position)
                .to({
                    y: startY + 150
                }, 1500)
                .easing(TWEEN.Easing.Quadratic.InOut)
                .repeat(Infinity)
                .yoyo(true)
                .start();

            // If it's a proxy with a 3D model, link them
            if (infospot.modelObj) {
                infospot.bounceTween = tween;
            }
        }

        const STORAGE_BASE = "{{ Storage::url('') }}".replace(/\/$/, '') + '/';
        const normalizePath = (path) => path ? (path.startsWith('/') ? path.substring(1) : path) : '';
        const textureLoader = new THREE.TextureLoader();
        const hdLoader = document.getElementById('hd-loader');
        let selectedResolution = localStorage.getItem('vtour_res') || 'high'; // low, medium, high

        function getOrCreatePanorama(sceneId) {
            if (panoramas[sceneId]) return panoramas[sceneId];

            const sceneData = tourData.scenes.find(s => s.id == sceneId);
            if (!sceneData) return null;
            const lowUrl = sceneData.low_res_path ? (STORAGE_BASE + normalizePath(sceneData.low_res_path)) : (STORAGE_BASE +
                normalizePath(sceneData.high_res_path));
            const midUrl = sceneData.medium_res_path ? (STORAGE_BASE + normalizePath(sceneData.medium_res_path)) : null;
            const highUrl = STORAGE_BASE + normalizePath(sceneData.high_res_path);

            const pano = new PANOLENS.ImagePanorama(lowUrl);
            pano.loadStage = 0; // 0: low, 1: mid, 2: high
            pano.cachedTextures = {}; // Store textures for switching back
            panoramas[sceneId] = pano;

            // Capture the initial low-res texture into cache once pano loads it
            pano.addEventListener('load', () => {
                if (pano.material && pano.material.map && !pano.cachedTextures[0]) {
                    pano.cachedTextures[0] = pano.material.map;
                    console.log(`[Cache] Initial SD texture captured for ${sceneData.name}`);
                }
            });

            const applyTextureToPano = (texture, stage) => {
                if (!texture) return;

                const updateMaterial = (mat) => {
                    if (!mat) return;
                    if (Array.isArray(mat)) {
                        mat.forEach(m => updateMaterial(m));
                        return;
                    }

                    // Crucial: Update the map and trigger renewal
                    mat.map = texture;
                    if (mat.uniforms) {
                        if (mat.uniforms.tDiffuse) mat.uniforms.tDiffuse.value = texture;
                        if (mat.uniforms.tEquirect) mat.uniforms.tEquirect.value = texture;
                    }
                    mat.needsUpdate = true;
                };

                updateMaterial(pano.material);
                pano.traverse((node) => {
                    if (node.isMesh) updateMaterial(node.material);
                });

                // Also update the internal texture reference Panolens might use
                pano.texture = texture;
                pano.loadStage = stage;
            };

            const updatePanoTexture = (texture, stage, stageName) => {
                pano.cachedTextures[stage] = texture;

                // Block update based on selected resolution
                if (selectedResolution === 'low' && stage >= 1) return;
                if (selectedResolution === 'medium' && stage >= 2) return;

                // Only apply if it's an UPGRADE or the EXACT same as target
                // If we want to allow downgrades, we'll handle that in a separate function
                if (pano.loadStage < stage) {
                    console.log(`- ${stageName} applied for ${sceneData.name}`);
                    applyTextureToPano(texture, stage);
                }
            };

            pano.isLoading = false;
            const startLoading = () => {
                // IMPORTANT: Recalculate targetStage every time startLoading is called
                // to respect global resolution changes since the pano was created.
                const targetStage = selectedResolution === 'low' ? 0 : (selectedResolution === 'medium' ? 1 : 2);

                // If already at target or higher, or currently working, stop.
                if (pano.loadStage >= targetStage || pano.isLoading) return;

                // Priority 1: Check Cache first for target stage
                if (pano.cachedTextures[targetStage]) {
                    console.log(`[Cache] Applying stage ${targetStage} immediately for ${sceneData.name}`);
                    applyTextureToPano(pano.cachedTextures[targetStage], targetStage);
                    hdLoader.classList.remove('visible');
                    return;
                }

                pano.isLoading = true;

                const onFinally = () => {
                    pano.isLoading = false;
                    if (viewer.panorama === pano) {
                        const currentTarget = selectedResolution === 'low' ? 0 : (selectedResolution === 'medium' ?
                            1 : 2);
                        if (pano.loadStage >= currentTarget) hdLoader.classList.remove('visible');
                    }
                };

                console.log(`[Network] Start loading for ${sceneData.name}, Target: ${selectedResolution}`);

                if (midUrl && targetStage >= 1 && !pano.cachedTextures[1]) {
                    textureLoader.load(midUrl, (texMid) => {
                        updatePanoTexture(texMid, 1, 'MEDIUM');
                        if (targetStage >= 2 && !pano.cachedTextures[2]) {
                            textureLoader.load(highUrl, (texHigh) => {
                                updatePanoTexture(texHigh, 2, 'HIGH');
                                onFinally();
                            }, undefined, (err) => {
                                onFinally();
                            });
                        } else {
                            onFinally();
                        }
                    }, undefined, (err) => {
                        // Fallback to high directly if mid fails
                        if (targetStage >= 2) {
                            textureLoader.load(highUrl, (texHigh) => {
                                updatePanoTexture(texHigh, 2, 'HIGH');
                                onFinally();
                            }, undefined, (err) => {
                                onFinally();
                            });
                        } else {
                            onFinally();
                        }
                    });
                } else if (targetStage >= 2 && !pano.cachedTextures[2]) {
                    textureLoader.load(highUrl, (texHigh) => {
                        updatePanoTexture(texHigh, 2, 'HIGH');
                        onFinally();
                    }, undefined, (err) => {
                        onFinally();
                    });
                } else {
                    onFinally();
                }
            };

            // Start loading process
            pano.retryLoading = startLoading;
            startLoading();

            pano.addEventListener('enter-fade-start', () => {
                // Logic moved to switchScene for more reliable execution across all call paths
            });
            pano.addEventListener('leave', () => {
                hdLoader.classList.remove('visible');
            });

            // Attach infospots to this new panorama
            if (sceneData.infospots) {
                (async () => {
                    for (const ispotData of sceneData.infospots) {
                        let ispot;
                        let modelObj = null;

                        // Position Handling
                        let pos;
                        if (ispotData.type === '3d' || ispotData.type === 'image' || ispotData.is_perspective) {
                            pos = new THREE.Vector3(ispotData.position_x, ispotData.position_y, ispotData.position_z);
                        } else {
                            pos = new THREE.Vector3(ispotData.position_x, ispotData.position_y, ispotData.position_z).normalize()
                                .multiplyScalar(4000);
                        }

                        // Check for direct 3D model (.glb only) - ENFORCE type '3d'
                        if (ispotData.type === '3d' && ispotData.model_path && ispotData.model_path.toLowerCase().endsWith('.glb')) {
                            try {
                                const modelUrl = "{{ url('storage') }}/" + ispotData.model_path + "?v={{ time() }}";
                                console.log(`[3D-Load] Spot: ${ispotData.id}, URL: ${modelUrl}`);
                                modelObj = await loadGLB(modelUrl, ispotData);
                                
                                const transparentPixel = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
                                ispot = new PANOLENS.Infospot(2000, transparentPixel);
                                ispot.is3DModel = true;
                                ispot.modelObj = modelObj;

                                modelObj.position.copy(pos);
                                pano.add(modelObj);
                                addBounce(ispot);
                                ispot.syncPosition = true;
                            } catch (e) {
                                console.error(`[3D-Error] Spot: ${ispotData.id}:`, e);
                            }
                        }

                        if (!ispot) {
                            // Determine the texture/icon URL
                            let textureUrl = arrowUrl;
                            if (ispotData.type === 'info') textureUrl = infoUrl;
                            if (ispotData.type === '3d') textureUrl = threedUrl;
                            
                            // If it's a 2D floating image (custom icon override only for type 'image')
                            if (ispotData.type === 'image' && ispotData.model_path && !ispotData.model_path.toLowerCase().endsWith('.glb')) {
                                textureUrl = "{{ url('storage') }}/" + ispotData.model_path + "?v={{ time() }}";
                            }

                            console.log(`[Icon-Load] Spot: ${ispotData.id}, Type: ${ispotData.type}, URL: ${textureUrl}`);

                            if (ispotData.is_perspective || ispotData.type === 'image') {
                                // Render as 3D Mesh for perspective mode
                                const geometry = new THREE.PlaneGeometry(600, 600);
                                const texture = new THREE.TextureLoader().load(textureUrl);
                                const material = new THREE.MeshBasicMaterial({
                                    map: texture,
                                    transparent: true,
                                    side: THREE.DoubleSide,
                                    alphaTest: 0.1,
                                    depthTest: false,
                                    depthWrite: false
                                });
                                ispot = new THREE.Mesh(geometry, material);
                                ispot.renderOrder = 1000;
                                ispot.rotation.order = 'YXZ';
                                ispot.rotation.set(ispotData.rotation_x || 0, ispotData.rotation_y || 0, ispotData.rotation_z || 0);
                                ispot.scale.set(ispotData.scale_x || 1, ispotData.scale_y || 1, 1);
                                ispot.isPerspectiveMesh = true;
                            } else {
                                // Standard Billboard
                                ispot = new PANOLENS.Infospot(UNIFORM_SIZE, textureUrl);
                                ispot.renderOrder = 1000;
                            }
                        }

                        ispot.spotData = ispotData;
                        if (ispotData.type === 'nav') ispot.isNavMarker = true;

                        ispot.position.copy(pos);
                        ispot.addEventListener('click', () => {
                            handleSpotClick(ispotData);
                        });

                        // Smart Hover Logic
                        ispot.addEventListener('hoverenter', () => {
                            if (ispot.is3DModel) {
                                const s = 1000 * 1.2;
                                new TWEEN.Tween(ispot.modelObj.scale).to({
                                    x: (ispotData.scale_x || 1) * s,
                                    y: (ispotData.scale_y || 1) * s,
                                    z: (ispotData.scale_z || ispotData.scale_x || 1) * s
                                }, 300).easing(TWEEN.Easing.Back.Out).start();
                            } else if (ispot.isPerspectiveMesh) {
                                new TWEEN.Tween(ispot.scale).to({
                                    x: (ispotData.scale_x || 1) * 1.2,
                                    y: (ispotData.scale_y || 1) * 1.2,
                                    z: 1.2
                                }, 300).easing(TWEEN.Easing.Back.Out).start();
                            } else {
                                ispot.scale.set(1.3, 1.3, 1.3);
                            }
                        });

                        ispot.addEventListener('hoverleave', () => {
                            if (ispot.is3DModel) {
                                const s = 1000;
                                new TWEEN.Tween(ispot.modelObj.scale).to({
                                    x: (ispotData.scale_x || 1) * s,
                                    y: (ispotData.scale_y || 1) * s,
                                    z: (ispotData.scale_z || ispotData.scale_x || 1) * s
                                }, 300).easing(TWEEN.Easing.Back.Out).start();
                            } else if (ispot.isPerspectiveMesh) {
                                new TWEEN.Tween(ispot.scale).to({
                                    x: ispotData.scale_x || 1,
                                    y: ispotData.scale_y || 1,
                                    z: 1
                                }, 300).easing(TWEEN.Easing.Back.Out).start();
                            } else {
                                ispot.scale.set(1, 1, 1);
                            }
                        });

                        addBounce(ispot);
                        if (ispot) pano.add(ispot);
                    }
                })();
            }

            // Preload neighbors when this panorama loads
            pano.addEventListener('load', () => {
                preloadNeighbors(sceneId);
            });

            return pano;
        }

        function preloadNeighbors(sceneId) {
            const sceneData = tourData.scenes.find(s => s.id == sceneId);
            if (sceneData && sceneData.infospots) {
                sceneData.infospots.forEach(spot => {
                    if (spot.type === 'nav' && spot.target_scene_id) {
                        getOrCreatePanorama(spot.target_scene_id);
                    }
                });
            }
        }

        function handleSpotClick(spot) {
            if (spot.type === 'info' || spot.type === '3d' || spot.type === 'image') {
                // Build assets array — prefer assets relation, fallback to legacy model_path
                let assets = [];
                if (spot.assets && spot.assets.length > 0) {
                    assets = spot.assets.map(a => ({
                        file_type: a.file_type,
                        url: '{{ Storage::url('') }}/' + a.file_path,
                        label: a.label || null
                    }));
                } else if ((spot.type === '3d' || spot.type === 'image') && spot.model_path) {
                    const isGlb = spot.model_path.toLowerCase().endsWith('.glb');
                    assets = [{
                        file_type: isGlb ? '3d' : 'image',
                        url: STORAGE_BASE + normalizePath(spot.model_path),
                        label: isGlb ? '3D Model' : 'Original Image'
                    }];
                }
                let products = [];
                if (spot.products && spot.products.length > 0) {
                    products = spot.products.map(p => ({
                        id: p.id,
                        name: p.name,
                        description_id: p.description_id,
                        description_en: p.description_en,
                        researcher: p.researcher,
                        contact_person: p.contact_person,
                        assets: (p.assets || []).map(a => ({
                            file_type: a.file_type,
                            url: STORAGE_BASE + normalizePath(a.file_path),
                            label: a.label || null
                        }))
                    }));
                }
                openModal(spot.title || "Info", spot.content_id || "", spot.content_en || "", assets, products);
            } else if (spot.type === 'nav') {
                if (spot.target_scene_id) {
                    const targetPano = getOrCreatePanorama(spot.target_scene_id);
                    if (targetPano) {
                        const targetSceneData = spot.target_scene || spot.targetScene || tourData.scenes.find(s => s.id ==
                            spot.target_scene_id);
                        const targetSceneName = targetSceneData ? targetSceneData.name : "NEXT SCENE";
                        walkToTarget(targetPano, new THREE.Vector3(spot.position_x, spot.position_y, spot.position_z),
                            targetSceneName, "Navigasi", spot.target_scene_id);
                    }
                }
            }
        }

        // --- Manual Rotation & Cursor Logic ---
        let modelDragging = null;
        let lastPointerX = 0;
        let totalDragDistance = 0;
        const dragThreshold = 5; // Pixels to distinguish click vs drag

        container.addEventListener('pointerdown', (e) => {
            const rect = container.getBoundingClientRect();
            const mouse = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect
                .top) / rect.height) * 2 + 1);
            const raycaster = new THREE.Raycaster();
            raycaster.setFromCamera(mouse, viewer.getCamera());

            if (!viewer.panorama) return;
            const intersects = raycaster.intersectObjects(viewer.panorama.children, true);

            if (intersects.length > 0) {
                let target = intersects[0].object;
                while (target.parent && !target.is3DModel && !target.isPerspectiveMesh) {
                    target = target.parent;
                }

                if ((target.is3DModel || target.isPerspectiveMesh) && !target.isNavMarker) {
                    modelDragging = target;
                    modelDragging.isBeingDragged = false; // Don't start yet
                    lastPointerX = e.clientX;
                    totalDragDistance = 0;
                }
            }
        });

        container.addEventListener('pointermove', (e) => {
            const rect = container.getBoundingClientRect();
            const mouse = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect
                .top) / rect.height) * 2 + 1);

            if (modelDragging) {
                const deltaX = e.clientX - lastPointerX;
                totalDragDistance += Math.abs(deltaX);

                // Only start rotating after threshold
                if (!modelDragging.isBeingDragged && totalDragDistance > dragThreshold) {
                    modelDragging.isBeingDragged = true;
                    viewer.getControl().enabled = false;
                    container.style.cursor = 'grabbing';
                }

                if (modelDragging.isBeingDragged) {
                    if (modelDragging.is3DModel && modelDragging.modelObj) {
                        modelDragging.modelObj.rotation.y += deltaX * 0.01;
                    } else {
                        modelDragging.rotation.y += deltaX * 0.01;
                    }
                    lastPointerX = e.clientX;
                }
            } else {
                // Hover cursor logic
                const raycaster = new THREE.Raycaster();
                raycaster.setFromCamera(mouse, viewer.getCamera());
                if (!viewer.panorama) return;
                const intersects = raycaster.intersectObjects(viewer.panorama.children, true);

                let isOverModel = false;
                if (intersects.length > 0) {
                    let target = intersects[0].object;
                    while (target.parent && !target.is3DModel && !target.isPerspectiveMesh) {
                        target = target.parent;
                    }
                    if ((target.is3DModel || target.isPerspectiveMesh) && !target.isNavMarker) {
                        isOverModel = true;
                    }
                }
                container.style.cursor = isOverModel ? 'pointer' : '';
            }
        });

        window.addEventListener('pointerup', (e) => {
            if (modelDragging) {
                modelDragging.isBeingDragged = false;
                modelDragging = null;
                viewer.getControl().enabled = true;
                container.style.cursor = '';
            }
        });

        function walkToTarget(pano, targetPosition, title, subtitle, targetSceneId = null) {
            // Sembunyikan ikon di panorama lama agar tidak "mengikuti" saat transisi
            if (viewer.panorama) {
                viewer.panorama.children.forEach(c => {
                    if (c instanceof PANOLENS.Infospot || c.isPerspectiveMesh || c.is3DModel || c.isGLBModel) {
                        c.visible = false;
                        // Also hide the physical model if it's a 3D proxy
                        if (c.modelObj) c.modelObj.visible = false;
                    }
                });
            }

            viewer.tweenControlCenter(targetPosition, 500);
            setTimeout(() => {
                let startFov = viewer.camera.fov;
                let targetFovIn = 40;
                let duration = 600;
                let startTime = Date.now();

                function zoomIn() {
                    let elapsed = Date.now() - startTime;
                    let progress = Math.min(elapsed / duration, 1);
                    let eased = progress * progress * progress;
                    viewer.camera.fov = startFov + (targetFovIn - startFov) * eased;
                    viewer.camera.updateProjectionMatrix();

                    if (progress < 1) requestAnimationFrame(zoomIn);
                    else {
                        if (!pano.parent) viewer.add(pano);
                        viewer.setPanorama(pano);

                        // Check for upgrade on current pano every time we enter
                        const targetStage = selectedResolution === 'low' ? 0 : (selectedResolution === 'medium' ?
                            1 : 2);
                        if (pano.loadStage < targetStage) {
                            hdLoader.classList.add('visible');
                            if (pano.retryLoading) pano.retryLoading();
                        } else {
                            // If we already have a better texture than target (e.g. from previous high-res session)
                            // but user wants SD, we should swap back here just to be sure
                            if (pano.loadStage > targetStage) {
                                _applyCachedTexture(pano, targetStage);
                            }
                            hdLoader.classList.remove('visible');
                        }

                        // Pastikan visibilitas ikon di panorama baru sesuai dengan tombol toggle
                        const markersBtn = document.getElementById('toggle-markers');
                        const isMarkersEnabled = markersBtn ? markersBtn.classList.contains('btn-active') : true;
                        pano.children.forEach(c => {
                            if (c instanceof PANOLENS.Infospot || c.isPerspectiveMesh) c.visible =
                                isMarkersEnabled;
                        });

                        document.getElementById('scene-title').innerText = title;

                        // Update active status in scene list
                        document.querySelectorAll('.scene-card').forEach(card => {
                            card.classList.toggle('active', card.dataset.id == targetSceneId);
                        });
                        currentSceneData = tourData.scenes.find(s => s.id == targetSceneId);

                        // Safeguard: re-apply autoRotate state from the internal flag
                        const ctrl = viewer.getControl();
                        ctrl.autoRotate = viewer.autoRotate;

                        // Apply saved initial camera direction if target scene has one
                        const tsd = targetSceneId ? tourData.scenes.find(s => s.id == targetSceneId) : null;
                        if (tsd && (tsd.initial_lon !== 0 || tsd.initial_lat !== 0)) {
                            setTimeout(() => {
                                _applyInitialView(
                                    parseFloat(tsd.initial_lon),
                                    parseFloat(tsd.initial_lat)
                                );
                            }, 500);
                        }

                        startTime = Date.now();
                        zoomOut();
                    }
                }

                function zoomOut() {
                    let elapsed = Date.now() - startTime;
                    let progress = Math.min(elapsed / duration, 1);
                    let eased = 1 - Math.pow(1 - progress, 3);
                    viewer.camera.fov = targetFovIn + (startFov - targetFovIn) * eased;
                    viewer.camera.updateProjectionMatrix();
                    if (progress < 1) requestAnimationFrame(zoomOut);
                }
                zoomIn();
            }, 500);
        }

        function renderSceneList() {
            const listPanel = document.getElementById('scene-list-panel');
            if (!listPanel) return;
            listPanel.innerHTML = '';

            tourData.scenes.forEach(scene => {
                const card = document.createElement('div');
                card.className = `scene-card ${scene.id == currentSceneData?.id ? 'active' : ''}`;
                card.dataset.id = scene.id;

                const normalizePath = (path) => path ? (path.startsWith('/') ? path.substring(1) : path) : '';
                const thumbUrl = scene.thumbnail_path ? (STORAGE_BASE + normalizePath(scene.thumbnail_path)) : (
                    STORAGE_BASE + normalizePath(scene.high_res_path));
                card.innerHTML = `
                    <img src="${thumbUrl}" alt="${scene.name}">
                    <div class="scene-card-label">${scene.name}</div>
                `;

                card.onclick = () => {
                    if (scene.id == currentSceneData?.id) return;
                    const targetPano = getOrCreatePanorama(scene.id);
                    if (targetPano) {
                        walkToTarget(targetPano, new THREE.Vector3(0, 0, 0), scene.name, "Akses Langsung", scene
                            .id);
                    }
                };

                listPanel.appendChild(card);
            });
        }

        async function initTour() {
            infoUrl = await createStyledIcon('i', '#2563eb');
            arrowUrl = await createStyledIcon('⮝', '#4f46e5');
            threedUrl = await createStyledIcon('3D', '#7c3aed');

            viewer = new PANOLENS.Viewer({
                container: container,
                autoRotate: true,
                autoRotateSpeed: 0.5,
                controlBar: false,
                cameraFov: 100
            });

            // Add Lights for 3D Models
            const ambientLight = new THREE.AmbientLight(0xffffff, 1.2);
            viewer.add(ambientLight);
            const sunLight = new THREE.DirectionalLight(0xffffff, 0.8);
            sunLight.position.set(1000, 2000, 1000);
            viewer.add(sunLight);

            // OPTIMASI: Batasi pixel ratio maksimal ke 1.5 (seperti engine 3DVista).
            // Layar HP modern sering memaksakan ratio 3x atau 4x yang membuat GPU kelebihan beban saat render 3D.
            if (viewer.renderer) {
                viewer.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
            }

            // Invert scroll zoom direction
            const controls = viewer.getControl();
            const originalDollyIn = controls.dollyIn;
            controls.dollyIn = controls.dollyOut;
            controls.dollyOut = originalDollyIn;













            // Inisialisasi awal: Scene dengan urutan (order) terkecil otomatis jadi Start Scene
            let startSceneData = tourData.scenes[0];
            let startScene = startSceneData ? getOrCreatePanorama(startSceneData.id) : null;



            if (startScene) {
                viewer.add(startScene);
                startScene.addEventListener('load', () => {
                    loader.style.opacity = '0';
                    setTimeout(() => loader.style.display = 'none', 1000);

                    // Inisialisasi daftar scene
                    currentSceneData = startSceneData;
                    renderSceneList();

                    // Apply saved initial camera direction after controls settle
                    const lon0 = parseFloat(startSceneData?.initial_lon ?? 0);
                    const lat0 = parseFloat(startSceneData?.initial_lat ?? 0);
                    if (lon0 !== 0 || lat0 !== 0) {
                        setTimeout(() => _applyInitialView(lon0, lat0), 300);
                    }
                });
            } else {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 1000);
            }

            // Convert saved lon/lat back to a THREE.Vector3 and apply via tweenControlCenter
            function _applyInitialView(lon, lat) {
                // Inverse of: lon=atan2(dir.z,dir.x), lat=asin(dir.y)
                // Panolens: target = (sin(phi)*cos(theta), cos(phi), sin(phi)*sin(theta))
                //           phi = 90-lat (degrees), theta = lon (degrees)
                const toRad = Math.PI / 180;
                const phi = (90 - lat) * toRad;
                const theta = lon * toRad;
                const target = new THREE.Vector3(
                    Math.sin(phi) * Math.cos(theta),
                    Math.cos(phi),
                    Math.sin(phi) * Math.sin(theta)
                ).multiplyScalar(500);
                viewer.tweenControlCenter(target, 0);
            }



            document.getElementById('toggle-rotate').addEventListener('click', function() {
                const isAutoRotate = !viewer.getControl().autoRotate;
                viewer.getControl().autoRotate = isAutoRotate;
                viewer.autoRotate = isAutoRotate; // Sync Panolens internal flag
                this.classList.toggle('btn-active', isAutoRotate);
            });

            document.getElementById('toggle-fullscreen').addEventListener('click', function() {
                const docElm = document.documentElement;
                const rfs = docElm.requestFullscreen || docElm.webkitRequestFullScreen || docElm
                    .mozRequestFullScreen || docElm.msRequestFullscreen;
                const efs = document.exitFullscreen || document.webkitExitFullscreen || document
                    .mozCancelFullScreen || document.msExitFullscreen;
                const fse = document.fullscreenElement || document.webkitFullscreenElement || document
                    .mozFullScreenElement || document.msFullscreenElement;

                if (!fse) {
                    if (rfs) {
                        rfs.call(docElm);
                    } else {
                        // Fallback for iPhone/iOS which doesn't support Fullscreen API
                        docElm.classList.add('pseudo-fullscreen');
                        document.getElementById('toggle-fullscreen').classList.add('btn-active');
                        // Trigger a resize event to ensure Panolens adjusts
                        window.dispatchEvent(new Event('resize'));
                    }
                } else {
                    if (efs) {
                        efs.call(document);
                    } else {
                        docElm.classList.remove('pseudo-fullscreen');
                        document.getElementById('toggle-fullscreen').classList.remove('btn-active');
                        window.dispatchEvent(new Event('resize'));
                    }
                }
            });

            // Sync button state on system fullscreen change
            const syncFS = () => {
                const fse = document.fullscreenElement || document.webkitFullscreenElement || document
                    .mozFullScreenElement || document.msFullscreenElement;
                const isPseudo = document.documentElement.classList.contains('pseudo-fullscreen');
                document.getElementById('toggle-fullscreen').classList.toggle('btn-active', !!fse || isPseudo);
            };
            document.addEventListener('fullscreenchange', syncFS);

            // Resolution Menu Logic
            const resBtn = document.getElementById('active-res-btn');
            const resMenu = document.getElementById('res-menu');
            const resLabel = document.getElementById('current-res-label');

            resBtn.onclick = (e) => {
                e.stopPropagation();
                resMenu.classList.toggle('show');
            };

            document.addEventListener('click', () => resMenu.classList.remove('show'));

            resMenu.querySelectorAll('button').forEach(btn => {
                // Initialize UI based on stored resolution
                if (btn.dataset.res === selectedResolution) {
                    resMenu.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    resLabel.textContent = btn.textContent.split(' ')[0];
                }

                btn.onclick = () => {
                    const res = btn.dataset.res;
                    selectedResolution = res;
                    localStorage.setItem('vtour_res', res); // Save to cache

                    // UI Update
                    resMenu.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    resLabel.textContent = btn.textContent.split(' ')[0]; // Show SD, HD, or ULTRA

                    // Apply only to CURRENT panorama immediately to save bandwidth
                    if (viewer && viewer.panorama) {
                        const curPano = viewer.panorama;

                        // 1. Handle Downgrade: Switch back to cached textures
                        if (res === 'low' && curPano.cachedTextures[0]) {
                            _applyCachedTexture(curPano, 0);
                        } else if (res === 'medium' && curPano.cachedTextures[1]) {
                            _applyCachedTexture(curPano, 1);
                        } else if (res === 'high' && curPano.cachedTextures[2]) {
                            _applyCachedTexture(curPano, 2);
                        }

                        // 2. Handle Upgrade: Trigger download if not yet at target
                        if ((res === 'medium' && curPano.loadStage < 1) || (res === 'high' && curPano
                                .loadStage < 2)) {
                            _forceUpgrade(curPano);
                        }

                        // Handle loader visibility for current view
                        if ((res === 'low') || (res === 'medium' && curPano.loadStage >= 1) || (res ===
                                'high' && curPano.loadStage >= 2)) {
                            hdLoader.classList.remove('visible');
                        } else {
                            hdLoader.classList.add('visible');
                        }
                    }
                };
            });

            function _applyCachedTexture(pano, stage) {
                const texture = pano.cachedTextures[stage];
                if (!texture) return;

                const updateMaterial = (mat) => {
                    if (!mat) return;
                    if (Array.isArray(mat)) {
                        mat.forEach(m => updateMaterial(m));
                        return;
                    }
                    if (mat.map !== undefined) mat.map = texture;
                    if (mat.uniforms) {
                        if (mat.uniforms.tDiffuse) mat.uniforms.tDiffuse.value = texture;
                        if (mat.uniforms.tEquirect) mat.uniforms.tEquirect.value = texture;
                    }
                    mat.needsUpdate = true;
                };

                updateMaterial(pano.material);
                pano.traverse((node) => {
                    if (node.isMesh) updateMaterial(node.material);
                });
                pano.texture = texture;
                pano.loadStage = stage;
            }

            function _forceUpgrade(pano) {
                if (pano && pano.retryLoading) {
                    pano.retryLoading();
                }
            }
            document.addEventListener('webkitfullscreenchange', syncFS);
            document.addEventListener('mozfullscreenchange', syncFS);
            document.addEventListener('msfullscreenchange', syncFS);

            document.getElementById('toggle-markers').addEventListener('click', function() {
                const pano = viewer.panorama;
                const visible = !pano.children[0].visible;
                this.classList.toggle('btn-active', visible);
                pano.children.forEach(c => {
                    if (c instanceof PANOLENS.Infospot || c.isPerspectiveMesh) {
                        c.visible = visible;
                    }
                });
            });

            document.getElementById('ui-toggle').addEventListener('click', function() {
                const panel = document.getElementById('control-buttons-panel');
                const listPanel = document.getElementById('scene-list-panel');
                const isMinimized = panel.classList.toggle('minimized');
                if (listPanel) listPanel.classList.toggle('minimized', isMinimized);

                this.innerHTML = isMinimized ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-times"></i>';
                this.style.background = isMinimized ? 'rgba(255,255,255,0.05)' : '#6366f1';
                this.style.borderColor = isMinimized ? 'rgba(255,255,255,0.1)' : 'rgba(255,255,255,0.3)';
            });



            viewer.container.addEventListener('click', (e) => {
                if (!document.getElementById('coord-display').classList.contains('show')) return;
                const rect = container.getBoundingClientRect();
                const mouse = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY -
                    rect.top) / rect.height) * 2 + 1);
                const raycaster = new THREE.Raycaster();
                raycaster.setFromCamera(mouse, viewer.getCamera());
                const intersects = raycaster.intersectObjects(viewer.getScene().children, true);
                if (intersects.length > 0) {
                    const p = intersects[0].point;
                    document.getElementById('coord-display').innerHTML =
                        `Position: <b>set(${Math.round(p.x)}, ${Math.round(p.y)}, ${Math.round(p.z)})</b>`;
                }
            });
        }

        let currentModalLang = 'id';

        function playNarration() {
            if (!window.responsiveVoice) return;

            let htmlText = document.getElementById('tab-' + currentModalLang).innerHTML;
            let plainText = htmlText.replace(/<[^>]*>?/gm, '');
            if (!plainText.trim()) return;

            let voice = currentModalLang === 'id' ? "Indonesian Female" : "UK English Female";

            responsiveVoice.speak(plainText, voice, {
                onstart: () => {
                    document.getElementById('btn-play').classList.add('hidden');
                    document.getElementById('btn-stop').classList.remove('hidden');
                },
                onend: () => {
                    document.getElementById('btn-play').classList.remove('hidden');
                    document.getElementById('btn-stop').classList.add('hidden');
                }
            });
        }

        function stopNarration() {
            if (window.responsiveVoice) {
                responsiveVoice.cancel();
            }
            const btnPlay = document.getElementById('btn-play');
            const btnStop = document.getElementById('btn-stop');
            if (btnPlay) btnPlay.classList.remove('hidden');
            if (btnStop) btnStop.classList.add('hidden');
        }

        function switchTab(lang) {
            currentModalLang = lang;
            stopNarration();

            const tabs = ['id', 'en', 'researcher', 'contact'];
            tabs.forEach(t => {
                const btn = document.getElementById('btn-tab-' + t);
                const cont = document.getElementById('tab-' + t + '-container');
                const area = document.getElementById('tab-' + t);
                if (btn) btn.classList.remove('active');
                if (cont) cont.classList.remove('active');
                if (area) area.classList.remove('active');
            });

            document.getElementById('btn-tab-' + lang).classList.add('active');
            if (lang === 'id' || lang === 'en') {
                document.getElementById('tab-' + lang + '-container').classList.add('active');
            }
            document.getElementById('tab-' + lang).classList.add('active');

            // Move VO controls to active tab container (only for description tabs)
            const vo = document.getElementById('vo-controls-wrapper');
            if (lang === 'id' || lang === 'en') {
                const target = document.getElementById('tab-' + lang + '-container');
                if (vo && target) {
                    target.appendChild(vo);
                    vo.classList.remove('hidden');
                }
            } else {
                // If researcher or contact, hide VO controls
                if (vo) vo.classList.add('hidden');
            }
        }

        function toggleEnlarge() {
            const wrapper = document.getElementById('modal-layout-wrapper');
            const icon = document.getElementById('enlarge-icon');
            const isEnlarged = wrapper.classList.toggle('assets-enlarged');

            icon.className = isEnlarged ? 'fas fa-compress-arrows-alt text-xs' : 'fas fa-expand-arrows-alt text-xs';

            // Adapt carousel height
            const slides = document.querySelectorAll('.vc-slide img, .vc-slide .mv-wrap');
            slides.forEach(el => {
                el.style.height = isEnlarged ? '60vh' : '300px';
            });

            // If enlarged, hide text pane
            const textPane = document.getElementById('modal-pane-text');
            textPane.style.display = isEnlarged ? 'none' : 'block';
        }

        /* ---- Vanilla Carousel state ---- */
        let _vcIndex = 0;
        let _vcTotal = 0;
        let _currentAssets = [];
        let _currentProducts = [];
        let _activeProductId = null;
        let _spotTextId = '';
        let _spotTextEn = '';

        function vcGoto(idx) {
            _vcIndex = Math.max(0, Math.min(idx, _vcTotal - 1));

            // Fix: Translate percentage must be relative to the track's total width.
            // Since each slide is 100% of the container, the track is (_vcTotal * 100%) wide.
            // To move 1 slide, we move (100 / _vcTotal)%.
            const movePct = (_vcTotal > 0) ? (_vcIndex * (100 / _vcTotal)) : 0;
            document.getElementById('vc-track').style.transform = `translateX(-${movePct}%)`;

            // dots
            document.querySelectorAll('.vc-dot').forEach((d, i) => d.classList.toggle('active', i === _vcIndex));

            // update arrow state
            const prevBtn = document.getElementById('vc-prev');
            const nextBtn = document.getElementById('vc-next');
            if (prevBtn) prevBtn.disabled = _vcIndex === 0;
            if (nextBtn) nextBtn.disabled = _vcIndex === _vcTotal - 1;

            // Update Descriptions & Extra Tabs
            const activeProduct = _currentProducts.find(p => p.id === _activeProductId);

            const extraTabsWrapper = document.getElementById('extra-tabs-wrapper');
            const btnResearcher = document.getElementById('btn-tab-researcher');
            const btnContact = document.getElementById('btn-tab-contact');

            if (activeProduct) {
                document.getElementById('tab-id').innerHTML = activeProduct.description_id || '-';
                document.getElementById('tab-en').innerHTML = activeProduct.description_en || '-';

                document.getElementById('tab-researcher').innerHTML = activeProduct.researcher || '';
                document.getElementById('tab-contact').innerHTML = activeProduct.contact_person || '';

                // Show/Hide buttons based on content
                const hasResearcher = !!activeProduct.researcher && activeProduct.researcher !== '<p><br></p>';
                const hasContact = !!activeProduct.contact_person && activeProduct.contact_person !== '<p><br></p>';

                if (hasResearcher || hasContact) {
                    extraTabsWrapper.classList.remove('hidden');
                    btnResearcher.style.display = hasResearcher ? 'flex' : 'none';
                    btnContact.style.display = hasContact ? 'flex' : 'none';
                } else {
                    extraTabsWrapper.classList.add('hidden');
                }
            } else {
                // Fallback to spot's own description if no product is active (Legacy Group Mode)
                document.getElementById('tab-id').innerHTML = _spotTextId || '';
                document.getElementById('tab-en').innerHTML = _spotTextEn || '';
                extraTabsWrapper.classList.add('hidden');
            }

            // reset zoom on slide change
            _imgZoomReset();
        }

        document.getElementById('vc-prev').addEventListener('click', () => vcGoto(_vcIndex - 1));
        document.getElementById('vc-next').addEventListener('click', () => vcGoto(_vcIndex + 1));

        function buildCarousel(assets) {
            const track = document.getElementById('vc-track');
            const dotsEl = document.getElementById('vc-dots');
            const pane = document.getElementById('modal-pane-assets');

            // Check current enlargement state to apply correct height to new slides
            const isEnlarged = document.getElementById('modal-layout-wrapper').classList.contains('assets-enlarged');
            const slideHeight = isEnlarged ? '60vh' : '300px';

            // Reset
            track.innerHTML = '';
            dotsEl.innerHTML = '';
            _vcIndex = 0;
            _vcTotal = assets.length;
            _currentAssets = assets;

            if (!assets || assets.length === 0) {
                pane.style.display = 'none';
                return;
            }

            pane.style.display = 'block';
            track.style.width = (_vcTotal * 100) + '%'; // Force track to be (total * 100%) wide

            assets.forEach((asset, i) => {
                // --- Slide ---
                const slide = document.createElement('div');
                slide.className = 'vc-slide';
                slide.style.width = (100 / _vcTotal) + '%'; // Each slide is (1/total)% of the track

                const badge = document.createElement('span');
                badge.className = `vc-badge ${asset.file_type === '3d' ? 'vc-badge-3d' : 'vc-badge-2d'}`;
                badge.innerText = asset.file_type === '3d' ? '🧊 3D' : '🖼 Photo';
                slide.appendChild(badge);

                if (asset.file_type === '2d') {
                    const img = document.createElement('img');
                    img.src = asset.url;
                    img.alt = asset.label || 'Image';
                    img.loading = 'lazy';
                    img.style.height = slideHeight; // Apply current height state
                    slide.appendChild(img);
                } else {
                    const wrap = document.createElement('div');
                    wrap.className = 'mv-wrap';
                    wrap.style.height = slideHeight; // Apply current height state
                    wrap.innerHTML =
                        `<model-viewer src="${asset.url}" auto-rotate camera-controls shadow-intensity="1" touch-action="pan-y" loading="eager"></model-viewer>`;
                    slide.appendChild(wrap);
                }

                if (asset.label) {
                    const lbl = document.createElement('div');
                    lbl.className = 'vc-slide-label';
                    lbl.innerText = asset.label;
                    slide.appendChild(lbl);
                }

                track.appendChild(slide);

                // --- Dot ---
                if (assets.length > 1) {
                    const dot = document.createElement('button');
                    dot.className = 'vc-dot' + (i === 0 ? ' active' : '');
                    dot.addEventListener('click', () => vcGoto(i));
                    dotsEl.appendChild(dot);
                }
            });

            // Hide arrows & dots when single slide
            const isSingle = assets.length === 1;
            document.getElementById('vc-prev').style.display = isSingle ? 'none' : 'flex';
            document.getElementById('vc-next').style.display = isSingle ? 'none' : 'flex';
            dotsEl.style.display = isSingle ? 'none' : 'flex';

            // Reset track position & zoom
            vcGoto(0);
            track.style.transition = 'none';
            setTimeout(() => {
                track.style.transition = '';
            }, 50);

            // Disable prev on first slide
            document.getElementById('vc-prev').disabled = true;
            document.getElementById('vc-next').disabled = isSingle;

            vcGoto(0);
        }

        /* ---- Image zoom via mouse scroll ---- */
        let _imgScale = 1;
        const _imgMinScale = 1;
        const _imgMaxScale = 5;

        function _imgApplyTransform(img) {
            img.style.transform = `scale(${_imgScale})`;
            img.style.cursor = _imgScale > 1 ? 'grab' : 'zoom-in';
        }

        function _imgZoomReset() {
            _imgScale = 1;
            document.querySelectorAll('.vc-slide img').forEach(img => {
                img.style.transform = '';
                img.style.transformOrigin = '50% 50%';
                img.style.cursor = 'zoom-in';
            });
        }

        // Zoom via mouse scroll (Desktop)
        document.getElementById('vc-wrap').addEventListener('wheel', (e) => {
            const img = e.target.closest('.vc-slide')?.querySelector('img');
            if (!img) return;

            e.preventDefault();
            const delta = -e.deltaY;
            const factor = 1.1;
            const direction = delta > 0 ? 1 : -1;
            const newScale = direction > 0 ? _imgScale * factor : _imgScale / factor;

            if (newScale < _imgMinScale || newScale > _imgMaxScale) return;

            const rect = img.getBoundingClientRect();
            const mouseX = (e.clientX - rect.left) / _imgScale;
            const mouseY = (e.clientY - rect.top) / _imgScale;
            const originX = (mouseX / (rect.width / _imgScale)) * 100;
            const originY = (mouseY / (rect.height / _imgScale)) * 100;

            _imgScale = newScale;
            img.style.transformOrigin = `${originX}% ${originY}%`;
            _imgApplyTransform(img);
        }, {
            passive: false
        });

        // Zoom via Pinch (Mobile)
        (() => {
            let initialDist = 0;
            let initialScale = 1;

            document.getElementById('vc-wrap').addEventListener('touchstart', (e) => {
                if (e.touches.length === 2) {
                    initialDist = Math.hypot(
                        e.touches[0].pageX - e.touches[1].pageX,
                        e.touches[0].pageY - e.touches[1].pageY
                    );
                    initialScale = _imgScale;
                    e.preventDefault();
                }
            }, {
                passive: false
            });

            document.getElementById('vc-wrap').addEventListener('touchmove', (e) => {
                const img = e.target.closest('.vc-slide')?.querySelector('img');
                if (img && e.touches.length === 2 && initialDist > 0) {
                    const dist = Math.hypot(
                        e.touches[0].pageX - e.touches[1].pageX,
                        e.touches[0].pageY - e.touches[1].pageY
                    );
                    const delta = dist / initialDist;
                    _imgScale = Math.max(_imgMinScale, Math.min(_imgMaxScale, initialScale * delta));
                    _imgApplyTransform(img);
                    e.preventDefault();
                }
            }, {
                passive: false
            });
        })();

        // Pan while zoomed in — shift transform-origin to pan
        (() => {
            let dragging = false,
                startX = 0,
                startY = 0;
            let originXpct = 50,
                originYpct = 50;
            let activeImg = null;

            document.getElementById('vc-wrap').addEventListener('mousedown', (e) => {
                const img = e.target.closest('.vc-slide')?.querySelector('img');
                if (!img || _imgScale <= 1) return;
                dragging = true;
                activeImg = img;
                startX = e.clientX;
                startY = e.clientY;
                // Read current origin
                const orig = (img.style.transformOrigin || '50% 50%').split(' ');
                originXpct = parseFloat(orig[0]) || 50;
                originYpct = parseFloat(orig[1]) || 50;
                img.style.cursor = 'grabbing';
                e.preventDefault();
            });

            window.addEventListener('mousemove', (e) => {
                if (!dragging || !activeImg) return;
                const rect = activeImg.getBoundingClientRect();
                // How much in % did we move relative to image size
                const dxPct = (e.clientX - startX) / rect.width * 100 / _imgScale;
                const dyPct = (e.clientY - startY) / rect.height * 100 / _imgScale;
                startX = e.clientX;
                startY = e.clientY;
                originXpct = Math.max(0, Math.min(100, originXpct - dxPct));
                originYpct = Math.max(0, Math.min(100, originYpct - dyPct));
                activeImg.style.transformOrigin = `${originXpct.toFixed(2)}% ${originYpct.toFixed(2)}%`;
            });

            window.addEventListener('mouseup', () => {
                if (dragging && activeImg) activeImg.style.cursor = _imgScale > 1 ? 'grab' : 'zoom-in';
                dragging = false;
                activeImg = null;
            });

            // Double-click to reset zoom
            document.getElementById('vc-wrap').addEventListener('dblclick', (e) => {
                const img = e.target.closest('.vc-slide')?.querySelector('img');
                if (!img) return;
                _imgZoomReset();
            });
        })();

        function openModal(title, textId, textEn, assets = [], products = []) {
            _currentAssets = assets;
            _currentProducts = products;
            _activeProductId = null;
            _spotTextId = textId;
            _spotTextEn = textEn;

            // Title Logic: If 1 product, use product name as title
            let displayTitle = title;
            if (products && products.length === 1) {
                displayTitle = products[0].name;
            }
            document.getElementById('modal-title').innerText = displayTitle;

            // Handle Product Tabs
            const tabsContainer = document.getElementById('product-tabs');
            tabsContainer.innerHTML = '';
            if (products && products.length > 1) {
                tabsContainer.classList.remove('hidden');
                products.forEach((p, i) => {
                    const btn = document.createElement('button');
                    btn.className = 'product-tab' + (i === 0 ? ' active' : '');
                    btn.dataset.id = p.id;
                    btn.innerText = p.name;
                    btn.onclick = () => switchProduct(p.id);
                    tabsContainer.appendChild(btn);
                });
                // Switch to first product
                switchProduct(products[0].id);
            } else if (products && products.length === 1) {
                tabsContainer.classList.add('hidden');
                switchProduct(products[0].id);
            } else {
                tabsContainer.classList.add('hidden');
                // No products, just show assets (legacy)
                document.getElementById('tab-id').innerHTML = '';
                document.getElementById('tab-en').innerHTML = '';
                buildCarousel(assets);
            }

            switchTab('id');

            if (viewer) {
                viewer.autoRotate = false;
                if (viewer.getControl()) viewer.getControl().autoRotate = false;
            }

            document.getElementById('modal').classList.add('active');
        }

        function switchProduct(productId) {
            _activeProductId = productId;
            const product = _currentProducts.find(p => p.id === productId);
            if (product) {
                document.querySelectorAll('.product-tab').forEach(tab => {
                    tab.classList.toggle('active', tab.dataset.id == productId);
                });
                buildCarousel(product.assets);
            }
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            modal.classList.remove('active');

            // Reset enlarge state on close
            const wrapper = document.getElementById('modal-layout-wrapper');
            const icon = document.getElementById('enlarge-icon');
            if (wrapper && wrapper.classList.contains('assets-enlarged')) {
                wrapper.classList.remove('assets-enlarged');
                if (icon) icon.className = 'fas fa-expand-arrows-alt text-xs';
                const slides = document.querySelectorAll('.vc-slide img, .vc-slide .mv-wrap');
                slides.forEach(el => el.style.height = '300px');
                document.getElementById('modal-pane-text').style.display = 'block';
            }

            stopNarration();

            const isAutoRotateOn = document.getElementById('toggle-rotate').classList.contains('btn-active');
            if (viewer) {
                viewer.autoRotate = isAutoRotateOn;
                if (viewer.getControl()) viewer.getControl().autoRotate = isAutoRotateOn;
            }
        }

        /* ---- Site Plan System ---- */
        const sitePlanOverlay = document.getElementById('site-plan-overlay');
        const activeMapContainer = document.getElementById('active-map-container');

        document.getElementById('toggle-map').addEventListener('click', () => {
            const plans = tourData.site_plans || tourData.sitePlans;
            if (plans && plans.length > 0) {
                const firstPlanId = plans[0].id;
                loadMap(firstPlanId);
                sitePlanOverlay.classList.remove('invisible');
                sitePlanOverlay.classList.add('opacity-100');
            } else {
                alert('No site plans available for this tour.');
            }
        });

        function closeSitePlan() {
            sitePlanOverlay.classList.add('invisible');
            sitePlanOverlay.classList.remove('opacity-100');
        }

        function loadMap(planId) {
            const plans = tourData.site_plans || tourData.sitePlans;
            const plan = plans.find(p => p.id == planId);
            if (!plan) return;

            // Update tab UI
            document.querySelectorAll('.plan-tab-btn').forEach(btn => {
                const isActive = btn.dataset.id == planId;
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('bg-primary/20', isActive);
                btn.classList.toggle('border-primary/50', isActive);
                btn.classList.toggle('text-white/60', !isActive);
            });

            // Reset Map Zoom
            _mapScale = 1;
            _mapOriginX = 50;
            _mapOriginY = 50;
            activeMapContainer.style.transform = 'scale(1)';
            activeMapContainer.style.transformOrigin = '50% 50%';

            const lowUrl = plan.low_res_path ? `/storage/${plan.low_res_path}` : (plan.high_res_path ?
                `/storage/${plan.high_res_path}` : `/storage/${plan.image_path}`);
            const midUrl = plan.medium_res_path ? `/storage/${plan.medium_res_path}` : null;
            const highUrl = plan.high_res_path ? `/storage/${plan.high_res_path}` : `/storage/${plan.image_path}`;

            activeMapContainer.innerHTML = `
                <img id="active-map-img" src="${lowUrl}" class="max-w-full max-h-[60vh] w-auto h-auto block rounded-lg shadow-2xl pointer-events-none object-contain transition-opacity">
                <div id="map-hotspots-layer" class="absolute inset-0"></div>
            `;

            const imgElement = document.getElementById('active-map-img');
            if (midUrl) {
                const midImg = new Image();
                midImg.src = midUrl;
                midImg.onload = () => {
                    if (imgElement) imgElement.src = midUrl;
                    const highImg = new Image();
                    highImg.src = highUrl;
                    highImg.onload = () => {
                        if (imgElement) imgElement.src = highUrl;
                    };
                };
            } else if (highUrl !== lowUrl) {
                const highImg = new Image();
                highImg.src = highUrl;
                highImg.onload = () => {
                    if (imgElement) imgElement.src = highUrl;
                };
            }

            const layer = document.getElementById('map-hotspots-layer');
            plan.hotspots.forEach(hs => {
                const marker = document.createElement('div');
                marker.className = 'absolute -translate-x-1/2 -translate-y-1/2 group cursor-pointer';
                marker.style.left = hs.x + '%';
                marker.style.top = hs.y + '%';

                const sceneName = hs.scene ? hs.scene.name : 'Unknown Scene';

                marker.innerHTML = `
                    <div class="relative">
                        <div class="w-4 h-4 md:w-6 md:h-6 bg-primary rounded-full border-2 md:border-4 border-white shadow-lg animate-pulse hover:scale-125 transition-transform"></div>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 bg-slate-900 text-white px-2 py-1 rounded text-[8px] font-bold uppercase tracking-widest whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-xl">
                            ${sceneName}
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
                        </div>
                    </div>
                `;

                marker.onclick = () => {
                    const targetPano = getOrCreatePanorama(hs.scene_id);
                    if (targetPano) {
                        closeSitePlan();
                        walkToTarget(targetPano, new THREE.Vector3(0, 0, 0), sceneName, "Site Plan Navigation",
                            hs.scene_id);
                    }
                };

                layer.appendChild(marker);
            });
        }

        /* ---- Map Zoom/Pan Logic ---- */
        let _mapScale = 1;
        let _mapOriginX = 50;
        let _mapOriginY = 50;
        let _mapDragging = false;
        let _mapStartX = 0;
        let _mapStartY = 0;

        const mapArea = document.getElementById('map-canvas-area');

        mapArea.addEventListener('wheel', (e) => {
            e.preventDefault();
            const delta = -e.deltaY;
            const factor = 1.1;
            const direction = delta > 0 ? 1 : -1;
            const newScale = direction > 0 ? _mapScale * factor : _mapScale / factor;

            if (newScale < 1 || newScale > 10) return;

            const rect = activeMapContainer.getBoundingClientRect();
            const mouseX = (e.clientX - rect.left) / _mapScale;
            const mouseY = (e.clientY - rect.top) / _mapScale;
            _mapOriginX = (mouseX / (rect.width / _mapScale)) * 100;
            _mapOriginY = (mouseY / (rect.height / _mapScale)) * 100;

            _mapScale = newScale;
            activeMapContainer.style.transformOrigin = `${_mapOriginX}% ${_mapOriginY}%`;
            activeMapContainer.style.transform = `scale(${_mapScale})`;
        }, {
            passive: false
        });

        mapArea.addEventListener('mousedown', (e) => {
            if (_mapScale <= 1) return;
            _mapDragging = true;
            _mapStartX = e.clientX;
            _mapStartY = e.clientY;
            e.preventDefault();
        });

        window.addEventListener('mousemove', (e) => {
            if (!_mapDragging) return;
            const rect = activeMapContainer.getBoundingClientRect();
            const dxPct = (e.clientX - _mapStartX) / rect.width * 100 / _mapScale;
            const dyPct = (e.clientY - _mapStartY) / rect.height * 100 / _mapScale;

            _mapStartX = e.clientX;
            _mapStartY = e.clientY;

            _mapOriginX = Math.max(0, Math.min(100, _mapOriginX - dxPct));
            _mapOriginY = Math.max(0, Math.min(100, _mapOriginY - dyPct));

            activeMapContainer.style.transformOrigin = `${_mapOriginX.toFixed(2)}% ${_mapOriginY.toFixed(2)}%`;
        });

        window.addEventListener('mouseup', () => {
            _mapDragging = false;
        });

        // Mobile Pinch Zoom for Map
        (() => {
            let initialDist = 0;
            let initialScale = 1;
            mapArea.addEventListener('touchstart', (e) => {
                if (e.touches.length === 2) {
                    initialDist = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e
                        .touches[1].pageY);
                    initialScale = _mapScale;
                    e.preventDefault();
                }
            }, {
                passive: false
            });
            mapArea.addEventListener('touchmove', (e) => {
                if (e.touches.length === 2 && initialDist > 0) {
                    const dist = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e
                        .touches[1].pageY);
                    _mapScale = Math.max(1, Math.min(10, initialScale * (dist / initialDist)));
                    activeMapContainer.style.transform = `scale(${_mapScale})`;
                    e.preventDefault();
                }
            }, {
                passive: false
            });
        })();

        initTour();
    </script>
</body>

</html>
