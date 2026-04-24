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
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Base element tweaks */
        model-viewer {
            width: 100%;
            height: 100%;
            --progress-bar-color: var(--color-primary);
        }

        /* Utilities */
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }

        /* =============================================
           VANILLA CAROUSEL — Asset Media Slider
           ============================================= */
        .vc-wrap {
            position: relative;
            width: 100%;
            border-radius: 18px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            overflow: hidden; /* hides sliding track; zoom handled via img transform */
        }
        .vc-track {
            display: flex;
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            will-change: transform;
        }
        .vc-slide {
            min-width: 100%;
            position: relative;
            flex-shrink: 0;
        }
        .vc-slide img {
            width: 100%;
            height: 300px;
            object-fit: contain;
            display: block;
            cursor: zoom-in;
            transform-origin: 50% 50%; /* overridden dynamically on wheel */
            transition: transform 0.15s ease;
            user-select: none;
            background: rgba(0,0,0,0.2);
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
            color: rgba(255,255,255,0.4);
            text-align: center;
            padding: 8px;
            background: rgba(0,0,0,0.35);
        }
        /* Nav arrows */
        .vc-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.15);
            background: rgba(10,15,30,0.75);
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
        .vc-btn:hover { background: rgba(99,102,241,0.75); }
        .vc-btn.vc-prev { left: 10px; }
        .vc-btn.vc-next { right: 10px; }
        .vc-btn:disabled { opacity: 0.2; cursor: default; }
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
            background: rgba(255,255,255,0.2);
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            padding: 0;
        }
        .vc-dot.active {
            background: #6366f1;
            transform: scale(1.5);
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
            border: 1px solid rgba(255,255,255,0.15);
            z-index: 5;
        }
        .vc-badge-2d { background: rgba(37,99,235,0.65); color: #bfdbfe; }
        .vc-badge-3d { background: rgba(124,58,237,0.65); color: #ddd6fe; }

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
            background: rgba(0,0,0,0.5);
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
            border-color: rgba(255,255,255,0.3);
            box-shadow: 0 10px 20px rgba(0,0,0,0.4);
        }
        .scene-card.active {
            border-color: #6366f1;
            box-shadow: 0 0 15px rgba(99,102,241,0.6);
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
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            color: white;
            font-size: 8px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="m-0 p-0 w-full h-full overflow-hidden bg-black font-outfit">

    <div id="loader" class="fixed top-0 left-0 w-full h-full bg-[#0f172a] flex flex-col items-center justify-center z-[50000] text-white transition-opacity duration-1000">
        <div class="w-[50px] h-[50px] border-[5px] border-white/5 border-t-primary rounded-full animate-spin-slow mb-[25px]"></div>
        <div class="font-[300] tracking-[6px] text-xs">CLEANING UI...</div>
    </div>

    <!-- Modal System -->
    <div id="modal" class="group fixed top-0 left-0 w-full h-full bg-black/60 backdrop-blur-[15px] flex items-center justify-center z-[20000] opacity-0 invisible transition-all duration-400 [&.active]:opacity-100 [&.active]:visible">
        <div class="bg-bg-glass border border-border-glass py-5 px-6 md:py-[20px] md:px-[25px] rounded-[25px] max-w-[1000px] w-[90%] max-h-[85vh] flex flex-col text-white transform scale-80 transition-transform duration-400 text-left relative scrollbar-none group-[.active]:scale-100 overflow-hidden">
            
            <div class="flex items-center justify-between mb-[15px]">
                <h2 id="modal-title" class="m-0 text-2xl">Info</h2>
                <div class="text-[1.2rem] text-white/40 cursor-pointer transition-all duration-300 z-10 w-[36px] h-[36px] flex items-center justify-center rounded-full bg-white/10 shrink-0 hover:text-white hover:bg-accent hover:rotate-90" onclick="closeModal()">
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
                        <button class="vc-btn vc-prev" id="vc-prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
                        <button class="vc-btn vc-next" id="vc-next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
                        
                        <!-- Enlarge Button -->
                        <button class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-black/50 backdrop-blur-md border border-white/20 text-white flex items-center justify-center cursor-pointer z-20 hover:bg-primary transition-all" onclick="toggleEnlarge()" title="Enlarge View">
                            <i class="fas fa-expand-arrows-alt text-xs" id="enlarge-icon"></i>
                        </button>

                        <div class="vc-dots" id="vc-dots"></div>
                    </div>
                </div>

                <!-- Text Pane -->
                <div class="flex-1 w-full" id="modal-pane-text">
                    <div class="flex flex-col w-full">
                        <div class="flex flex-wrap gap-[10px] mb-[15px] border-b border-white/10 pb-[10px]">
                            <button id="btn-tab-id" class="tab-btn active bg-white/5 border border-transparent text-white/60 py-1 px-2 rounded-md text-xs cursor-pointer transition-all duration-300 font-semibold hover:text-white hover:bg-white/10 hover:border-transparent [&.active]:bg-primary [&.active]:text-white [&.active]:border-white/20" onclick="switchTab('id')">Indonesia</button>
                            <button id="btn-tab-en" class="tab-btn bg-white/5 border border-transparent text-white/60 py-1 px-2 rounded-md text-xs cursor-pointer transition-all duration-300 font-semibold hover:text-white hover:bg-white/10 hover:border-transparent [&.active]:bg-primary [&.active]:text-white [&.active]:border-white/20" onclick="switchTab('en')">English</button>
                            <div class="grow min-w-[20px]"></div>
                            <button id="btn-play" class="tab-btn bg-white/5 border border-transparent text-white/60 py-1 px-2 rounded-md text-xs cursor-pointer transition-all duration-300 font-semibold hover:text-white hover:bg-white/10" onclick="playNarration()"><i class="fas fa-volume-up"></i></button>
                            <button id="btn-stop" class="tab-btn bg-rose-500/20 border border-rose-500/50 text-rose-500 py-1 px-2 rounded-md text-xs cursor-pointer transition-all duration-300 font-semibold hidden hover:bg-rose-500/30 hover:text-white hover:border-rose-500/70" onclick="stopNarration()"><i class="fas fa-stop"></i></button>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto max-h-[250px] md:max-h-[400px] pr-2 scrollbar-none">
                            <div class="leading-[1.6] text-white/80 text-justify hidden opacity-0 [&.active]:block [&.active]:animate-fade-in [&.active]:opacity-100 [&_p]:mt-0 [&_p]:mb-[1em]" id="tab-id"></div>
                            <div class="leading-[1.6] text-white/80 text-justify hidden opacity-0 [&.active]:block [&.active]:animate-fade-in [&.active]:opacity-100 [&_p]:mt-0 [&_p]:mb-[1em]" id="tab-en"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- 3D Canvas -->
    <div id="viewer-container" class="w-full h-screen bg-black"></div>

    <!-- UI Overlay -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none p-[15px] box-border z-[10000]">
        
        <div class="flex items-start justify-between w-full" id="overlay-top-wrapper">
            <!-- Left Group: Vertical Container -->
            <div class="flex flex-col items-start gap-[12px] pointer-events-auto" id="left-sidebar-container">
                <!-- Top Group: Responsive Toggle + Controls -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-[12px]" id="top-control-bar">
                    <!-- UI Toggle Button (Always Visible) -->
                    <div id="ui-toggle" class="w-[48px] h-[48px] bg-bg-glass backdrop-blur-[25px] border border-border-glass rounded-lg text-white flex items-center justify-center cursor-pointer transition-all duration-400 ease-[cubic-bezier(0.175,0.885,0.32,1.275)] shadow-[0_15px_30px_rgba(0,0,0,0.4)] hover:bg-primary hover:scale-110 hover:rotate-6 text-[1.1rem]">
                        <i class="fas fa-times"></i>
                    </div>

                    <!-- Control Buttons (Toggleable) -->
                    <div id="control-buttons-panel" class="bg-bg-glass backdrop-blur-[30px] border border-border-glass p-[6px] rounded-lg flex sm:flex-row flex-col gap-[6px] shadow-[0_20px_40px_rgba(0,0,0,0.4)] transition-all duration-500 ease-in-out [&.minimized]:opacity-0 [&.minimized]:-translate-x-[20px] sm:[&.minimized]:-translate-x-[20px] [&.minimized]:-translate-y-[10px] [&.minimized]:pointer-events-none [&.minimized]:blur-[10px]">
                        <button id="toggle-rotate" class="btn-action btn-active bg-white/5 hover:bg-white/15 [&.btn-active]:bg-primary/35 border border-border-glass [&.btn-active]:border-primary/80 text-white/90 w-[38px] h-[38px] rounded-[12px] cursor-pointer inline-flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95" title="Auto Rotation"><i class="fas fa-sync-alt text-[14px] text-primary"></i></button>
                        
                        <button id="toggle-markers" class="btn-action btn-active bg-white/5 hover:bg-white/15 [&.btn-active]:bg-primary/35 border border-border-glass [&.btn-active]:border-primary/80 text-white/90 w-[38px] h-[38px] rounded-[12px] cursor-pointer inline-flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95" title="Show Markers"><i class="bi bi-eye-fill text-[14px] text-primary"></i></button>

                        <button id="toggle-map" class="btn-action bg-white/5 hover:bg-white/15 border border-border-glass text-white/90 w-[38px] h-[38px] rounded-[12px] cursor-pointer inline-flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95" title="Site Plan"><i class="fas fa-map text-[14px] text-primary"></i></button>

                        <button id="toggle-fullscreen" class="btn-action bg-white/5 hover:bg-white/15 [&.btn-active]:bg-primary/35 border border-border-glass [&.btn-active]:border-primary/80 text-white/90 w-[38px] h-[38px] rounded-[12px] cursor-pointer inline-flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95" title="Full Screen"><i class="fas fa-expand text-[14px] text-primary"></i></button>
                    </div>
                </div>

                <!-- Scene List Sidebar (Toggleable, Below controls) -->
                <div id="scene-list-panel" class="flex flex-col gap-[6px] max-h-[calc(100vh-250px)] overflow-y-auto scrollbar-none transition-all duration-500 ease-in-out [&.minimized]:opacity-0 [&.minimized]:-translate-x-[40px] [&.minimized]:pointer-events-none [&.minimized]:blur-[10px]">
                    <!-- Items dynamic -->
                </div>
            </div>

            <!-- Right Panel: Scene Title (Always Visible) -->
            <div class="bg-bg-glass backdrop-blur-[30px] border border-border-glass p-[10px_22px] rounded-lg pointer-events-auto shadow-[0_30px_60px_rgba(0,0,0,0.5)] origin-right text-right">
                @php
                    $startScene = $tour->scenes->where('is_start_scene', true)->first() ?? $tour->scenes->first();
                @endphp
                <h1 id="scene-title" class="m-0 text-[16px] font-bold text-white tracking-[0.8px]">{{ $startScene->name ?? 'SCENE' }}</h1>
                <p id="scene-subtitle" class="mt-[4px] m-0 text-[9px] text-white/50 font-bold tracking-[2px] uppercase">{{ strtoupper($tour->name) }}</p>
            </div>
        </div>
        
        <!-- Bottom Section (Flex empty space helper) -->
        <div class="flex-1"></div>

        <!-- Coord Pill -->
        <div class="bg-bg-glass backdrop-blur-[20px] border border-border-glass px-[25px] py-[10px] rounded-[50px] text-white font-mono text-[13px] self-start pointer-events-auto opacity-0 invisible transition-all duration-400 shadow-[0_10px_30px_rgba(0,0,0,0.5)] [&.show]:opacity-100 [&.show]:visible" id="coord-display">Ambil kordinat dengan klik ruangan...</div>
    </div>

    <!-- Site Plan Overlay -->
    <div id="site-plan-overlay" class="fixed top-0 left-0 w-full h-full bg-black/80 backdrop-blur-xl z-[25000] opacity-0 invisible transition-all duration-500 flex items-center justify-center p-6 md:p-12">
        <div class="relative bg-[#0f172a]/90 border border-white/10 py-5 px-6 md:py-[20px] md:px-[25px] rounded-[25px] max-w-[1000px] w-[90%] max-h-[85vh] flex flex-col items-center shadow-2xl">
            <!-- Header -->
            <div class="w-full flex items-center justify-between mb-6 px-4">
                <div>
                    <h2 class="text-white text-xl font-black uppercase tracking-tighter">Site Plan & Maps</h2>
                    <p class="text-white/40 text-[9px] font-bold uppercase tracking-[2px] mt-1">Select a location to navigate</p>
                </div>
                <button class="w-10 h-10 bg-white/10 rounded-full text-white/60 hover:text-white hover:bg-rose-500 transition-all flex items-center justify-center cursor-pointer" onclick="closeSitePlan()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Map Canvas -->
            <div class="flex-1 w-full relative overflow-hidden flex items-center justify-center rounded-[24px] bg-black/20 border border-white/5 p-4" id="map-canvas-area">
                <div id="active-map-container" class="relative inline-block transition-transform duration-200 ease-out origin-center cursor-grab active:cursor-grabbing">
                    <!-- Map Image will be injected here -->
                </div>
            </div>

            <!-- Map Selector -->
            @if($tour->sitePlans->count() > 1)
                <div class="w-full flex items-center justify-center gap-4 mt-6 overflow-x-auto py-2 scrollbar-none">
                    @foreach($tour->sitePlans as $plan)
                        <button class="px-6 py-2 bg-white/5 border border-white/10 rounded-full text-white/60 text-[10px] font-bold uppercase tracking-widest hover:bg-primary/20 hover:text-white hover:border-primary/50 transition-all whitespace-nowrap cursor-pointer plan-tab-btn" data-id="{{ $plan->id }}" onclick="loadMap({{ $plan->id }})">
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
            canvas.width = 150; canvas.height = 150;
            const ctx = canvas.getContext('2d');
            
            if(rotation) {
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
            const delta = clock.getDelta();
            mixers.forEach(mixer => mixer.update(delta));
        }

        async function loadGLB(url, spotData) {
            return new Promise((resolve, reject) => {
                loader3d.load(url, (gltf) => {
                    const model = gltf.scene;
                    
                    // Scale up significantly for world-space visibility
                    const s = 100;
                    model.position.set(0, 0, 0); // Position is handled by proxy
                    model.rotation.set(spotData.rotation_x || 0, spotData.rotation_y || 0, spotData.rotation_z || 0);
                    model.scale.set(
                        (spotData.scale_x || 1) * s, 
                        (spotData.scale_y || 1) * s, 
                        (spotData.scale_z || spotData.scale_x || 1) * s
                    );
                    
                    model.is3DModel = true;
                    
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
            new TWEEN.Tween(infospot.position)
                .to({ y: startY + 200 }, 1000)
                .easing(TWEEN.Easing.Quadratic.InOut)
                .repeat(Infinity)
                .yoyo(true)
                .start();
        }

        function getOrCreatePanorama(sceneId) {
            if (panoramas[sceneId]) return panoramas[sceneId];

            const sceneData = tourData.scenes.find(s => s.id == sceneId);
            if (!sceneData) return null;

            const imageUrl = '{{ Storage::url("") }}' + sceneData.image_path;
            const pano = new PANOLENS.ImagePanorama(imageUrl);
            panoramas[sceneId] = pano;

            // Attach infospots to this new panorama
            if (sceneData.infospots) {
                sceneData.infospots.forEach(async (spot) => {
                    let ispot;
                    let modelObj = null;

                    // Position Normalization (Ensure inside the 5000 radius sphere)
                    const pos = new THREE.Vector3(spot.position_x, spot.position_y, spot.position_z).normalize().multiplyScalar(4000);
                    
                    // Check for direct 3D model
                    if (spot.model_path) {
                        try {
                            const modelUrl = '{{ url('storage') }}/' + spot.model_path;
                            modelObj = await loadGLB(modelUrl, spot);
                            
                            // Create a Proxy Infospot for interaction
                            ispot = new PANOLENS.Infospot(800, PANOLENS.DataImage.Info);
                            ispot.material.opacity = 0;
                            ispot.add(modelObj);
                            ispot.is3DModel = true;
                            ispot.modelObj = modelObj;
                        } catch (e) {
                            console.error("GLB load failed:", e);
                        }
                    }

                    if (!ispot) {
                        if (spot.is_perspective) {
                            // Render as 3D Mesh for perspective mode
                            const iconUrl = (spot.type === 'info') ? infoUrl : (spot.type === '3d' ? threedUrl : arrowUrl);
                            const geometry = new THREE.PlaneGeometry(600, 600);
                            const texture = new THREE.TextureLoader().load(iconUrl);
                            const material = new THREE.MeshBasicMaterial({ 
                                map: texture, transparent: true, side: THREE.DoubleSide,
                                alphaTest: 0.1, depthTest: false, depthWrite: false
                            });
                            ispot = new THREE.Mesh(geometry, material);
                            ispot.renderOrder = 999;
                            ispot.rotation.order = 'YXZ';
                            ispot.rotation.set(spot.rotation_x || 0, spot.rotation_y || 0, spot.rotation_z || 0);
                            ispot.scale.set(spot.scale_x || 1, spot.scale_y || 1, 1);
                            ispot.isPerspectiveMesh = true;
                        } else {
                            // Standard Billboard
                            const iconUrl = (spot.type === 'info') ? infoUrl : (spot.type === '3d' ? threedUrl : arrowUrl);
                            ispot = new PANOLENS.Infospot(UNIFORM_SIZE, iconUrl);
                        }
                    }

                    ispot.position.copy(pos);
                    ispot.addEventListener('click', () => { handleSpotClick(spot); });

                    // Smart Hover Logic
                    ispot.addEventListener('hoverenter', () => {
                        if (ispot.is3DModel) {
                            const s = 100 * 1.2;
                            new TWEEN.Tween(ispot.modelObj.scale).to({ 
                                x: (spot.scale_x || 1) * s, y: (spot.scale_y || 1) * s, z: (spot.scale_z || spot.scale_x || 1) * s 
                            }, 300).easing(TWEEN.Easing.Back.Out).start();
                        } else if (ispot.isPerspectiveMesh) {
                            new TWEEN.Tween(ispot.scale).to({ x: (spot.scale_x || 1) * 1.2, y: (spot.scale_y || 1) * 1.2, z: 1.2 }, 300).easing(TWEEN.Easing.Back.Out).start();
                        } else {
                            ispot.scale.set(1.3, 1.3, 1.3);
                        }
                    });

                    ispot.addEventListener('hoverleave', () => {
                        if (ispot.is3DModel) {
                            const s = 100;
                            new TWEEN.Tween(ispot.modelObj.scale).to({ 
                                x: (spot.scale_x || 1) * s, y: (spot.scale_y || 1) * s, z: (spot.scale_z || spot.scale_x || 1) * s 
                            }, 300).easing(TWEEN.Easing.Back.Out).start();
                        } else if (ispot.isPerspectiveMesh) {
                            new TWEEN.Tween(ispot.scale).to({ x: spot.scale_x || 1, y: spot.scale_y || 1, z: 1 }, 300).easing(TWEEN.Easing.Back.Out).start();
                        } else {
                            ispot.scale.set(1, 1, 1);
                        }
                    });

                    if (!ispot.is3DModel) addBounce(ispot);
                    if (ispot) pano.add(ispot);
                });
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
            if (spot.type === 'info' || spot.type === '3d') {
                // Build assets array — prefer assets relation, fallback to legacy model_path
                let assets = [];
                if (spot.assets && spot.assets.length > 0) {
                    assets = spot.assets.map(a => ({
                        file_type: a.file_type,
                        url: '{{ Storage::url("") }}' + a.file_path,
                        label: a.label || null
                    }));
                } else if (spot.model_path) {
                    assets = [{ file_type: '3d', url: '{{ Storage::url("") }}' + spot.model_path, label: null }];
                }
                openModal(spot.title || "Info", spot.content_id || "", spot.content_en || "", assets);
            } else if (spot.type === 'nav') {
                if (spot.target_scene_id) {
                    const targetPano = getOrCreatePanorama(spot.target_scene_id);
                    if (targetPano) {
                        const targetSceneData = spot.target_scene || spot.targetScene || tourData.scenes.find(s => s.id == spot.target_scene_id);
                        const targetSceneName = targetSceneData ? targetSceneData.name : "NEXT SCENE";
                        walkToTarget(targetPano, new THREE.Vector3(spot.position_x, spot.position_y, spot.position_z), targetSceneName, "Navigasi", spot.target_scene_id);
                    }
                }
            }
        }

        function walkToTarget(pano, targetPosition, title, subtitle, targetSceneId = null) {
            // Sembunyikan ikon di panorama lama agar tidak "mengikuti" saat transisi
            if(viewer.panorama) {
                viewer.panorama.children.forEach(c => {
                    if (c instanceof PANOLENS.Infospot || c.isPerspectiveMesh) c.visible = false;
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
                        
                        // Pastikan visibilitas ikon di panorama baru sesuai dengan tombol toggle
                        const markersBtn = document.getElementById('toggle-markers');
                        const isMarkersEnabled = markersBtn ? markersBtn.classList.contains('btn-active') : true;
                        pano.children.forEach(c => {
                            if (c instanceof PANOLENS.Infospot || c.isPerspectiveMesh) c.visible = isMarkersEnabled;
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
                
                const imageUrl = '{{ Storage::url("") }}' + scene.image_path;
                card.innerHTML = `
                    <img src="${imageUrl}" alt="${scene.name}">
                    <div class="scene-card-label">${scene.name}</div>
                `;

                card.onclick = () => {
                    if (scene.id == currentSceneData?.id) return;
                    const targetPano = getOrCreatePanorama(scene.id);
                    if (targetPano) {
                        walkToTarget(targetPano, new THREE.Vector3(0, 0, 0), scene.name, "Akses Langsung", scene.id);
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






            






            // Inisialisasi awal hanya untuk scene pertama
            let startSceneData = tourData.scenes.find(s => s.is_start_scene) || tourData.scenes[0];
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
                const phi   = (90 - lat) * toRad;
                const theta = lon * toRad;
                const target = new THREE.Vector3(
                    Math.sin(phi) * Math.cos(theta),
                    Math.cos(phi),
                    Math.sin(phi) * Math.sin(theta)
                ).multiplyScalar(500);
                viewer.tweenControlCenter(target, 0);
            }



            document.getElementById('toggle-rotate').addEventListener('click', function () {
                const isAutoRotate = !viewer.getControl().autoRotate;
                viewer.getControl().autoRotate = isAutoRotate;
                viewer.autoRotate = isAutoRotate; // Sync Panolens internal flag
                this.classList.toggle('btn-active', isAutoRotate);
            });

            document.getElementById('toggle-fullscreen').addEventListener('click', function () {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                    this.classList.add('btn-active');
                } else {
                    document.exitFullscreen();
                    this.classList.remove('btn-active');
                }
            });

            document.getElementById('toggle-markers').addEventListener('click', function () {
                const pano = viewer.panorama;
                const visible = !pano.children[0].visible;
                this.classList.toggle('btn-active', visible);
                pano.children.forEach(c => { 
                    if (c instanceof PANOLENS.Infospot || c.isPerspectiveMesh) {
                        c.visible = visible;
                    }
                });
            });

            document.getElementById('ui-toggle').addEventListener('click', function () {
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
                const mouse = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect.top) / rect.height) * 2 + 1);
                const raycaster = new THREE.Raycaster();
                raycaster.setFromCamera(mouse, viewer.getCamera());
                const intersects = raycaster.intersectObjects(viewer.getScene().children, true);
                if (intersects.length > 0) {
                    const p = intersects[0].point;
                    document.getElementById('coord-display').innerHTML = `Position: <b>set(${Math.round(p.x)}, ${Math.round(p.y)}, ${Math.round(p.z)})</b>`;
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

            document.getElementById('btn-play').style.display = 'none';
            document.getElementById('btn-stop').style.display = 'inline-block';

            responsiveVoice.speak(plainText, voice, {
                onstart: () => {
                    document.getElementById('btn-play').style.display = 'none';
                    document.getElementById('btn-stop').style.display = 'inline-block';
                },
                onend: () => {
                    document.getElementById('btn-play').style.display = 'inline-block';
                    document.getElementById('btn-stop').style.display = 'none';
                }
            });
        }

        function stopNarration() {
            if (window.responsiveVoice) {
                responsiveVoice.cancel();
            }
            const btnPlay = document.getElementById('btn-play');
            const btnStop = document.getElementById('btn-stop');
            if (btnPlay) btnPlay.style.display = 'inline-block';
            if (btnStop) btnStop.style.display = 'none';
        }

        function switchTab(lang) {
            currentModalLang = lang;
            stopNarration();

            document.getElementById('btn-tab-id').classList.remove('active');
            document.getElementById('btn-tab-en').classList.remove('active');
            document.getElementById('tab-id').classList.remove('active');
            document.getElementById('tab-en').classList.remove('active');

            document.getElementById('btn-tab-' + lang).classList.add('active');
            document.getElementById('tab-' + lang).classList.add('active');
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

        function vcGoto(idx) {
            _vcIndex = Math.max(0, Math.min(idx, _vcTotal - 1));
            document.getElementById('vc-track').style.transform = `translateX(-${_vcIndex * 100}%)`;

            // dots
            document.querySelectorAll('.vc-dot').forEach((d, i) => d.classList.toggle('active', i === _vcIndex));

            // update arrow state
            const prevBtn = document.getElementById('vc-prev');
            const nextBtn = document.getElementById('vc-next');
            if (prevBtn) prevBtn.disabled = _vcIndex === 0;
            if (nextBtn) nextBtn.disabled = _vcIndex === _vcTotal - 1;

            // reset zoom on slide change
            _imgZoomReset();
        }

        document.getElementById('vc-prev').addEventListener('click', () => vcGoto(_vcIndex - 1));
        document.getElementById('vc-next').addEventListener('click', () => vcGoto(_vcIndex + 1));

        function buildCarousel(assets) {
            const track  = document.getElementById('vc-track');
            const dotsEl = document.getElementById('vc-dots');
            const pane   = document.getElementById('modal-pane-assets');

            // Reset
            track.innerHTML  = '';
            dotsEl.innerHTML = '';
            _vcIndex = 0;
            _vcTotal = assets.length;

            if (!assets || assets.length === 0) {
                pane.style.display = 'none';
                return;
            }

            pane.style.display = 'block';

            assets.forEach((asset, i) => {
                // --- Slide ---
                const slide = document.createElement('div');
                slide.className = 'vc-slide';

                const badge = document.createElement('span');
                badge.className = `vc-badge ${asset.file_type === '3d' ? 'vc-badge-3d' : 'vc-badge-2d'}`;
                badge.innerText = asset.file_type === '3d' ? '🧊 3D' : '🖼 Photo';
                slide.appendChild(badge);

                if (asset.file_type === '2d') {
                    const img = document.createElement('img');
                    img.src     = asset.url;
                    img.alt     = asset.label || 'Image';
                    img.loading = 'lazy';
                    slide.appendChild(img);
                } else {
                    const wrap = document.createElement('div');
                    wrap.className = 'mv-wrap';
                    wrap.innerHTML = `<model-viewer src="${asset.url}" auto-rotate camera-controls shadow-intensity="1" touch-action="pan-y" loading="eager"></model-viewer>`;
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
            _imgZoomReset();
            track.style.transition = 'none';
            track.style.transform  = 'translateX(0)';
            setTimeout(() => { track.style.transition = ''; }, 50);

            // Disable prev on first slide
            document.getElementById('vc-prev').disabled = true;
            document.getElementById('vc-next').disabled = isSingle;
        }

        /* ---- Image zoom via mouse scroll ---- */
        let _imgScale      = 1;
        const _imgMinScale = 1;
        const _imgMaxScale = 5;

        function _imgApplyTransform(img) {
            img.style.transform = `scale(${_imgScale})`;
            img.style.cursor    = _imgScale > 1 ? 'grab' : 'zoom-in';
        }

        function _imgZoomReset() {
            _imgScale = 1;
            document.querySelectorAll('.vc-slide img').forEach(img => {
                img.style.transform      = '';
                img.style.transformOrigin = '50% 50%';
                img.style.cursor         = 'zoom-in';
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
        }, { passive: false });

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
            }, { passive: false });

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
            }, { passive: false });
        })();

        // Pan while zoomed in — shift transform-origin to pan
        (() => {
            let dragging = false, startX = 0, startY = 0;
            let originXpct = 50, originYpct = 50;
            let activeImg = null;

            document.getElementById('vc-wrap').addEventListener('mousedown', (e) => {
                const img = e.target.closest('.vc-slide')?.querySelector('img');
                if (!img || _imgScale <= 1) return;
                dragging  = true;
                activeImg = img;
                startX    = e.clientX;
                startY    = e.clientY;
                // Read current origin
                const orig = (img.style.transformOrigin || '50% 50%').split(' ');
                originXpct = parseFloat(orig[0]) || 50;
                originYpct = parseFloat(orig[1]) || 50;
                img.style.cursor = 'grabbing';
                e.preventDefault();
            });

            window.addEventListener('mousemove', (e) => {
                if (!dragging || !activeImg) return;
                const rect  = activeImg.getBoundingClientRect();
                // How much in % did we move relative to image size
                const dxPct = (e.clientX - startX) / rect.width  * 100 / _imgScale;
                const dyPct = (e.clientY - startY) / rect.height * 100 / _imgScale;
                startX = e.clientX;
                startY = e.clientY;
                originXpct = Math.max(0, Math.min(100, originXpct - dxPct));
                originYpct = Math.max(0, Math.min(100, originYpct - dyPct));
                activeImg.style.transformOrigin = `${originXpct.toFixed(2)}% ${originYpct.toFixed(2)}%`;
            });

            window.addEventListener('mouseup', () => {
                if (dragging && activeImg) activeImg.style.cursor = _imgScale > 1 ? 'grab' : 'zoom-in';
                dragging  = false;
                activeImg = null;
            });

            // Double-click to reset zoom
            document.getElementById('vc-wrap').addEventListener('dblclick', (e) => {
                const img = e.target.closest('.vc-slide')?.querySelector('img');
                if (!img) return;
                _imgZoomReset();
            });
        })();

        function openModal(title, textId, textEn, assets = []) {
            document.getElementById('modal-title').innerText = title;
            document.getElementById('tab-id').innerHTML  = textId || '';
            document.getElementById('tab-en').innerHTML  = textEn || textId || '';

            switchTab('id');

            if (viewer) {
                viewer.autoRotate = false;
                if (viewer.getControl()) viewer.getControl().autoRotate = false;
            }

            buildCarousel(assets);

            document.getElementById('modal').classList.add('active');
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

            activeMapContainer.innerHTML = `
                <img src="/storage/${plan.image_path}" class="max-w-full max-h-[60vh] w-auto h-auto block rounded-lg shadow-2xl pointer-events-none object-contain">
                <div id="map-hotspots-layer" class="absolute inset-0"></div>
            `;

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
                        walkToTarget(targetPano, new THREE.Vector3(0, 0, 0), sceneName, "Site Plan Navigation", hs.scene_id);
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
        }, { passive: false });

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
                    initialDist = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e.touches[1].pageY);
                    initialScale = _mapScale;
                    e.preventDefault();
                }
            }, { passive: false });
            mapArea.addEventListener('touchmove', (e) => {
                if (e.touches.length === 2 && initialDist > 0) {
                    const dist = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e.touches[1].pageY);
                    _mapScale = Math.max(1, Math.min(10, initialScale * (dist / initialDist)));
                    activeMapContainer.style.transform = `scale(${_mapScale})`;
                    e.preventDefault();
                }
            }, { passive: false });
        })();

        initTour();
    </script>
</body>
</html>