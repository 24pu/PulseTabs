<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
/**
 * 架子鼓节拍器
 *
 * @package custom
 */
?>
<?php $this->need('header.php'); ?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- 标题区 -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-dark flex items-center gap-2">
                <i class="fas fa-drumstick-bite text-accent"></i> 架子鼓节拍器
                <span class="ml-2 text-xs bg-accent/10 text-accent px-2 py-0.5 rounded-full">军鼓 · 镲片</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">重拍(第1拍)军鼓 · 其余镲片 | 源自 24Pu PULSE 引擎</p>
        </div>

        <div class="p-5 space-y-6">
            <!-- 视觉摆锤区域 -->
            <div class="bg-gray-50 rounded-2xl p-4">
                <div class="relative h-20 bg-white rounded-full overflow-hidden shadow-inner border border-gray-200 cursor-pointer" id="visualizer">
                    <div id="beatBar" class="absolute top-0 left-0 w-12 h-full bg-gradient-to-r from-accent to-accent-dark rounded-full transition-all duration-75 shadow-lg" style="width: 12%;"></div>
                    <div id="beatMarks" class="absolute inset-0 flex pointer-events-none">
                        <!-- 动态生成拍号标记 -->
                    </div>
                </div>
            </div>

            <!-- 控制区域：拍号 + BPM -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- 拍号选择 -->
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <div class="text-sm font-semibold text-accent flex items-center justify-center gap-1 mb-2">
                        <i class="fas fa-chart-simple"></i> 拍号
                    </div>
                    <div class="flex justify-center gap-2" id="timeSigGroup">
                        <button data-beats="2" class="ts-option px-4 py-2 rounded-full text-sm font-medium transition bg-white text-gray-700 border border-gray-200 hover:border-accent">2/4</button>
                        <button data-beats="3" class="ts-option px-4 py-2 rounded-full text-sm font-medium transition bg-white text-gray-700 border border-gray-200 hover:border-accent">3/4</button>
                        <button data-beats="4" class="ts-option active px-4 py-2 rounded-full text-sm font-medium transition bg-accent text-white border border-accent shadow-sm">4/4</button>
                        <button data-beats="6" class="ts-option px-4 py-2 rounded-full text-sm font-medium transition bg-white text-gray-700 border border-gray-200 hover:border-accent">6/8</button>
                    </div>
                </div>

                <!-- BPM 控制 -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="text-sm font-semibold text-accent flex items-center justify-center gap-1 mb-2">
                        <i class="fas fa-tachometer-alt"></i> 速度 (BPM)
                    </div>
                    <input type="range" id="bpmSlider" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-accent" min="40" max="240" value="120" step="1">
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-sm text-gray-500">♩ =</span>
                        <span class="text-2xl font-mono font-bold text-accent" id="sliderBpmValue">120</span>
                        <span class="text-sm text-gray-500">BPM</span>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 mt-3">
                        <button class="preset-btn text-xs px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-accent hover:text-white transition" data-bpm="60">慢板 60</button>
                        <button class="preset-btn text-xs px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-accent hover:text-white transition" data-bpm="90">行板 90</button>
                        <button class="preset-btn text-xs px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-accent hover:text-white transition" data-bpm="120">中板 120</button>
                        <button class="preset-btn text-xs px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-accent hover:text-white transition" data-bpm="160">快板 160</button>
                    </div>
                </div>
            </div>

            <!-- 控制按钮 -->
            <div class="flex gap-3">
                <button id="startBtn" class="flex-1 bg-accent hover:bg-accent-dark text-white font-semibold py-3 rounded-full transition shadow-sm flex items-center justify-center gap-2">
                    <i class="fas fa-play"></i> 启动节拍器
                </button>
                <button id="stopBtn" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 rounded-full transition flex items-center justify-center gap-2">
                    <i class="fas fa-stop"></i> 停止
                </button>
            </div>

            <!-- 当前节拍显示 -->
            <div class="bg-gray-50 rounded-full py-3 px-4 text-center">
                <span class="text-gray-600">🥁 当前节拍</span>
                <span id="beatCounterDisplay" class="text-3xl font-bold text-accent ml-3">1</span>
            </div>
        </div>
    </div>
</div>

<!-- 全局节拍器脚本（原逻辑完整保留，仅样式类已切换为 Tailwind） -->
<script>
    (function(){
        const beatBar = document.getElementById('beatBar');
        const beatMarksContainer = document.getElementById('beatMarks');
        const timeSigOptions = document.querySelectorAll('.ts-option');
        const bpmSlider = document.getElementById('bpmSlider');
        const sliderBpmValueSpan = document.getElementById('sliderBpmValue');
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const beatCounterSpan = document.getElementById('beatCounterDisplay');
        const presetBtns = document.querySelectorAll('.preset-btn');

        let currentBPM = 120;
        let currentBeatsPerMeasure = 4;
        let isRunning = false;
        let scheduleTimeout = null;
        let currentBeatIndex = 0;
        let audioCtx = null;

        // 音频处理函数（与原版完全相同）
        function createNoiseBuffer(duration, amplitude) {
            if(!audioCtx) return null;
            const sampleRate = audioCtx.sampleRate;
            const bufferSize = sampleRate * duration;
            const buffer = audioCtx.createBuffer(1, bufferSize, sampleRate);
            const data = buffer.getChannelData(0);
            for (let i = 0; i < bufferSize; i++) {
                data[i] = (Math.random() * 2 - 1) * amplitude;
            }
            return buffer;
        }
        
        function playSnare() {
            if(!audioCtx) return;
            const now = audioCtx.currentTime;
            const noiseBuffer = createNoiseBuffer(0.22, 0.58);
            if(!noiseBuffer) return;
            const noiseSrc = audioCtx.createBufferSource();
            noiseSrc.buffer = noiseBuffer;
            const bandpass = audioCtx.createBiquadFilter();
            bandpass.type = 'bandpass';
            bandpass.frequency.value = 1750;
            bandpass.Q.value = 1.2;
            const gainNoise = audioCtx.createGain();
            gainNoise.gain.setValueAtTime(0.42, now);
            gainNoise.gain.exponentialRampToValueAtTime(0.0001, now + 0.22);
            noiseSrc.connect(bandpass);
            bandpass.connect(gainNoise);
            gainNoise.connect(audioCtx.destination);
            noiseSrc.start();
            noiseSrc.stop(now + 0.22);
            
            const osc = audioCtx.createOscillator();
            const gainOsc = audioCtx.createGain();
            osc.type = 'triangle';
            osc.frequency.value = 230;
            gainOsc.gain.setValueAtTime(0.26, now);
            gainOsc.gain.exponentialRampToValueAtTime(0.0001, now + 0.12);
            osc.connect(gainOsc);
            gainOsc.connect(audioCtx.destination);
            osc.start();
            osc.stop(now + 0.12);
        }
        
        function playHiHat() {
            if(!audioCtx) return;
            const now = audioCtx.currentTime;
            const duration = 0.1;
            const amplitude = 0.44;
            const noiseBuffer = createNoiseBuffer(duration, amplitude);
            if(!noiseBuffer) return;
            const noiseSrc = audioCtx.createBufferSource();
            noiseSrc.buffer = noiseBuffer;
            const highpass = audioCtx.createBiquadFilter();
            highpass.type = 'highpass';
            highpass.frequency.value = 4600;
            highpass.Q.value = 1.1;
            const gainHat = audioCtx.createGain();
            gainHat.gain.setValueAtTime(0.34, now);
            gainHat.gain.exponentialRampToValueAtTime(0.0001, now + duration);
            noiseSrc.connect(highpass);
            highpass.connect(gainHat);
            gainHat.connect(audioCtx.destination);
            noiseSrc.start();
            noiseSrc.stop(now + duration);
        }
        
        function playTickWithDrum(isAccent) {
            if (!audioCtx) return;
            const playSound = () => {
                if (!audioCtx) return;
                if (isAccent) playSnare();
                else playHiHat();
            };
            if (audioCtx.state === 'running') {
                playSound();
            } else if (audioCtx.state === 'suspended') {
                audioCtx.resume().then(() => {
                    if (audioCtx.state === 'running') playSound();
                }).catch(e => console.warn);
            } else {
                playSound();
            }
        }

        function initAudioContext() {
            if (audioCtx) return audioCtx;
            try {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const silentBuffer = audioCtx.createBuffer(1, 1, 22050);
                const silentSrc = audioCtx.createBufferSource();
                silentSrc.buffer = silentBuffer;
                silentSrc.connect(audioCtx.destination);
                silentSrc.start();
                silentSrc.stop(audioCtx.currentTime + 0.001);
                return audioCtx;
            } catch(e) {
                console.warn("Web Audio API not supported", e);
                return null;
            }
        }

        function updateBeatMarks() {
            beatMarksContainer.innerHTML = '';
            const total = currentBeatsPerMeasure;
            for (let i = 0; i < total; i++) {
                const markSpan = document.createElement('span');
                markSpan.textContent = (i+1).toString();
                markSpan.className = "flex-1 text-center text-xs text-gray-400 pb-2 border-r border-gray-200 last:border-r-0";
                beatMarksContainer.appendChild(markSpan);
            }
            if (!isRunning) beatBar.style.left = `0%`;
            else updateBeatBarPosition(currentBeatIndex);
        }

        function updateBeatBarPosition(beatIdx) {
            const total = currentBeatsPerMeasure;
            if (total <= 0) return;
            const barWidthPercent = 12;
            const halfBar = barWidthPercent / 2;
            const maxLeft = 100 - barWidthPercent;
            const cellWidth = 100 / total;
            const targetCenter = (beatIdx + 0.5) * cellWidth;
            let leftPos = targetCenter - halfBar;
            leftPos = Math.min(maxLeft, Math.max(0, leftPos));
            beatBar.style.left = `${leftPos}%`;
        }

        function pulseVisual() {
            beatBar.classList.add('shadow-lg', 'scale-105');
            setTimeout(() => beatBar.classList.remove('shadow-lg', 'scale-105'), 85);
        }

        function triggerBeat() {
            if (!isRunning) return;
            const isAccent = (currentBeatIndex === 0);
            playTickWithDrum(isAccent);
            pulseVisual();
            beatCounterSpan.textContent = (currentBeatIndex + 1).toString();
            updateBeatBarPosition(currentBeatIndex);
            currentBeatIndex = (currentBeatIndex + 1) % currentBeatsPerMeasure;
        }

        function stopScheduler() {
            if (scheduleTimeout) {
                clearTimeout(scheduleTimeout);
                scheduleTimeout = null;
            }
        }

        function startSchedulerLoop() {
            if (!isRunning) return;
            const intervalMs = (60 / currentBPM) * 1000;
            let expectedTime = performance.now();
            function step() {
                if (!isRunning) return;
                triggerBeat();
                expectedTime += intervalMs;
                let drift = performance.now() - expectedTime;
                if (Math.abs(drift) > intervalMs * 0.5) expectedTime = performance.now() + intervalMs;
                let nextDelay = expectedTime - performance.now();
                if (nextDelay < 0) nextDelay = 0;
                if (nextDelay > intervalMs * 1.5) nextDelay = intervalMs;
                scheduleTimeout = setTimeout(step, nextDelay);
            }
            step();
        }

        function stopMetronome() {
            if (!isRunning && scheduleTimeout === null) {
                currentBeatIndex = 0;
                beatCounterSpan.textContent = "1";
                updateBeatBarPosition(0);
                return;
            }
            isRunning = false;
            stopScheduler();
            currentBeatIndex = 0;
            beatCounterSpan.textContent = "1";
            updateBeatBarPosition(0);
        }

        function startMetronome() {
            if (isRunning) stopMetronome();
            const ctx = initAudioContext();
            const beginStart = () => {
                if (isRunning) return;
                isRunning = true;
                currentBeatIndex = 0;
                beatCounterSpan.textContent = "1";
                updateBeatBarPosition(0);
                stopScheduler();
                startSchedulerLoop();
            };
            if (ctx && ctx.state === 'suspended') {
                ctx.resume().then(beginStart).catch(e => console.warn);
            } else {
                beginStart();
            }
        }

        function setBPM(newBpm, fromSlider = true) {
            let bpm = Math.min(240, Math.max(40, Math.floor(newBpm)));
            if (currentBPM === bpm) return;
            currentBPM = bpm;
            if (fromSlider) bpmSlider.value = currentBPM;
            sliderBpmValueSpan.innerText = currentBPM;
            if (isRunning) {
                stopScheduler();
                startSchedulerLoop();
            }
        }

        function setTimeSignature(beats) {
            if (currentBeatsPerMeasure === beats) return;
            currentBeatsPerMeasure = beats;
            timeSigOptions.forEach(opt => {
                const optBeats = parseInt(opt.getAttribute('data-beats'), 10);
                if (optBeats === beats) {
                    opt.classList.add('bg-accent', 'text-white', 'border-accent');
                    opt.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
                } else {
                    opt.classList.remove('bg-accent', 'text-white', 'border-accent');
                    opt.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
                }
            });
            updateBeatMarks();
            currentBeatIndex = 0;
            beatCounterSpan.textContent = "1";
            updateBeatBarPosition(0);
            if (isRunning) {
                stopScheduler();
                startSchedulerLoop();
            }
        }

        bpmSlider.addEventListener('input', (e) => setBPM(parseInt(e.target.value, 10), true));
        presetBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const bpmVal = parseInt(btn.getAttribute('data-bpm'), 10);
                if (!isNaN(bpmVal)) setBPM(bpmVal, true);
            });
        });
        timeSigOptions.forEach(opt => {
            opt.addEventListener('click', () => {
                const beats = parseInt(opt.getAttribute('data-beats'), 10);
                if (!isNaN(beats)) setTimeSignature(beats);
            });
        });
        startBtn.addEventListener('click', startMetronome);
        stopBtn.addEventListener('click', () => {
            if (isRunning) stopMetronome();
            else {
                currentBeatIndex = 0;
                beatCounterSpan.textContent = "1";
                updateBeatBarPosition(0);
            }
        });

        function unlockAudio() {
            if (audioCtx && audioCtx.state === 'suspended') {
                audioCtx.resume().catch(e => console.warn);
            }
            document.removeEventListener('touchstart', unlockAudio);
            document.removeEventListener('click', unlockAudio);
        }
        document.addEventListener('touchstart', unlockAudio);
        document.addEventListener('click', unlockAudio);

        function init() {
            currentBPM = 120;
            currentBeatsPerMeasure = 4;
            bpmSlider.value = 120;
            sliderBpmValueSpan.innerText = "120";
            setTimeSignature(4);
            updateBeatMarks();
            beatBar.style.left = `0%`;
            beatCounterSpan.textContent = "1";
            initAudioContext();
        }
        init();
    })();
</script>

<?php $this->need('footer.php'); ?>