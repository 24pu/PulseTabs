<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
/**
 * 节拍细分练习器
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
                <i class="fas fa-drumstick-bite text-accent"></i> 节拍细分练习器
                <span class="ml-2 text-xs bg-accent/10 text-accent px-2 py-0.5 rounded-full">军鼓 · 镲片</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">军鼓强拍 · 镲片细分 | 附点·三连音·复合节奏训练</p>
        </div>

        <div class="p-5 space-y-6">
            <!-- 视觉区域 -->
            <div class="bg-gray-50 rounded-2xl p-4 space-y-4">
                <!-- 拍子按钮区域 (1 2 3 4 为按钮) -->
                <div id="beatBarContainer" class="flex justify-center gap-3 flex-wrap"></div>
                <!-- 细分点 -->
                <div id="subDivisionContainer" class="flex justify-center gap-2 flex-wrap"></div>
            </div>

            <!-- 节奏选择器 + BPM -->
            <div class="flex flex-col sm:flex-row gap-4 items-stretch">
                <div class="flex-1 bg-gray-50 rounded-xl p-3">
                    <div class="text-sm font-semibold text-accent flex items-center justify-center gap-1 mb-2">
                        <i class="fas fa-waveform"></i> 节奏型
                    </div>
                    <div id="rhythmSelector" class="flex flex-wrap justify-center gap-2">
                        <button data-rhythm="straight" class="rhythm-btn px-3 py-1.5 rounded-full text-sm font-medium transition bg-white text-gray-700 border border-gray-200 hover:border-accent">♩ 均匀四分</button>
                        <button data-rhythm="eighth" class="rhythm-btn px-3 py-1.5 rounded-full text-sm font-medium transition bg-white text-gray-700 border border-gray-200 hover:border-accent">♪ 八分音符</button>
                        <button data-rhythm="triplet" class="rhythm-btn px-3 py-1.5 rounded-full text-sm font-medium transition bg-white text-gray-700 border border-gray-200 hover:border-accent">♪♬ 三连音</button>
                        <button data-rhythm="dotted" class="rhythm-btn px-3 py-1.5 rounded-full text-sm font-medium transition bg-white text-gray-700 border border-gray-200 hover:border-accent">♩. 附点八分</button>
                        <button data-rhythm="sixteenth" class="rhythm-btn px-3 py-1.5 rounded-full text-sm font-medium transition bg-white text-gray-700 border border-gray-200 hover:border-accent">♫ 十六分</button>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 min-w-[140px]">
                    <div class="text-sm font-semibold text-accent flex items-center justify-center gap-1 mb-2">
                        <i class="fas fa-tachometer-alt"></i> 速度 (BPM)
                    </div>
                    <input type="number" id="tempoInput" value="80" step="1" min="40" max="220" class="w-full px-3 py-2 text-center border border-gray-200 rounded-full focus:ring-2 focus:ring-accent focus:border-accent">
                </div>
            </div>

            <!-- 控制按钮组 -->
            <div class="flex gap-3">
                <button id="playBtn" class="flex-1 bg-accent hover:bg-accent-dark text-white font-semibold py-2.5 rounded-full transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-play"></i> 播放节拍
                </button>
                <button id="stopBtn" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 rounded-full transition flex items-center justify-center gap-2">
                    <i class="fas fa-stop"></i> 停止
                </button>
                <button id="tapBtn" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 rounded-full transition flex items-center justify-center gap-2">
                    <i class="fas fa-tachometer-alt"></i> 轻敲定速
                </button>
            </div>

            <!-- 描述信息条 -->
            <div id="patternDescription" class="bg-gray-50 rounded-xl py-2.5 px-4 text-sm text-gray-600 border-l-4 border-accent">
                🎯 当前节奏: <strong>均匀四分音符</strong> (每拍一个重音) | 🥁 军鼓(强拍) + 镲片(细分)
            </div>

            <!-- 页脚链接 -->
            <div class="text-center text-xs text-gray-400 pt-2 border-t border-gray-100">
                <span class="inline-flex gap-3 justify-center flex-wrap">
                    <a href="https://24pu.com" class="hover:text-accent">🏠 24pu.com</a>
                    <a href="https://24pu.com/tools.html" class="hover:text-accent">工具大全</a>
                    <a href="https://24pu.com/tiaoyinqi.html" class="hover:text-accent">调音器</a>
                    <a href="https://24pu.com/jiepaiqi.html" class="hover:text-accent">节拍器</a>
                </span>
            </div>
        </div>
    </div>
</div>

<style>
    /* beat-icon 拍子按钮样式（浅色背景） */
    .beat-icon {
        background: #f1f5f9;  /* 浅灰色 */
        color: #3c4f70;
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        transition: all 0.1s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border-bottom: 2px solid #d1d9e8;
        cursor: default;   /* 保持视觉为按钮但不响应点击(节拍不由按钮触发) */
        border: none;
    }
    .beat-icon.active-beat {
        background: #FF5E00;
        color: white;
        box-shadow: 0 0 12px rgba(255,94,0,0.4);
        transform: scale(1.02);
        border-bottom-color: #ffd1b3;
    }
    .beat-icon.highlight-sub {
        background: #f0b27a;
        color: white;
        box-shadow: 0 0 10px #f7b86c;
        border-bottom-color: #ffe0b5;
    }
    .sub-unit {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.05s linear;
        background: #f1f5f9;
        color: #4f658d;
        border: 1px solid #e2e8f0;
    }
    .sub-unit.flash-sub {
        background: #FF5E00;
        color: white;
        transform: scale(1.1);
        box-shadow: 0 0 12px rgba(255,94,0,0.5);
        border-color: #ffd1b3;
    }
    .rhythm-btn.active-rhythm {
        background: #FF5E00 !important;
        color: white !important;
        border-color: #FF5E00 !important;
    }
    button.rhythm-btn {
        cursor: pointer;
    }
    button.rhythm-btn:active {
        transform: scale(0.97);
    }
</style>

<script>
    (function(){
        // DOM 元素
        const tempoInput = document.getElementById('tempoInput');
        const playBtn = document.getElementById('playBtn');
        const stopBtn = document.getElementById('stopBtn');
        const tapBtn = document.getElementById('tapBtn');
        const rhythmBtns = document.querySelectorAll('.rhythm-btn');
        const beatBarContainer = document.getElementById('beatBarContainer');
        const subDivisionContainer = document.getElementById('subDivisionContainer');
        const patternDescSpan = document.getElementById('patternDescription');

        let currentRhythm = 'straight';
        let bpm = 80;
        let isPlaying = false;
        let audioCtx = null;

        const rhythmPatterns = {
            straight:   [0],
            eighth:     [0, 0.5],
            triplet:    [0, 1/3, 2/3],
            dotted:     [0, 0.75],
            sixteenth:  [0, 0.25, 0.5, 0.75]
        };
        const rhythmMeta = {
            straight: { name: '均匀四分音符', desc: '每拍一个军鼓重音' },
            eighth:   { name: '八分音符', desc: '军鼓 + 踩镲，轻快律动' },
            triplet:  { name: '三连音', desc: '军鼓 + 镲片复合，摇摆感' },
            dotted:   { name: '附点八分', desc: '军鼓长音 + 清脆镲片短音' },
            sixteenth:{ name: '十六分音符', desc: '密集踩镲滚奏 + 军鼓重音' }
        };

        // 音频处理 (与原逻辑相同)
        function createNoiseBuffer(duration, amplitude) {
            if(!audioCtx) return null;
            const sampleRate = audioCtx.sampleRate;
            const bufferSize = sampleRate * duration;
            const buffer = audioCtx.createBuffer(1, bufferSize, sampleRate);
            const data = buffer.getChannelData(0);
            for (let i = 0; i < bufferSize; i++) data[i] = (Math.random() * 2 - 1) * amplitude;
            return buffer;
        }

        function playSnare() {
            if(!audioCtx) return;
            const now = audioCtx.currentTime;
            const noiseBuffer = createNoiseBuffer(0.25, 0.6);
            if(!noiseBuffer) return;
            const noiseSrc = audioCtx.createBufferSource();
            noiseSrc.buffer = noiseBuffer;
            const bandpass = audioCtx.createBiquadFilter();
            bandpass.type = 'bandpass';
            bandpass.frequency.value = 1800;
            bandpass.Q.value = 1.2;
            const gainNoise = audioCtx.createGain();
            gainNoise.gain.setValueAtTime(0.45, now);
            gainNoise.gain.exponentialRampToValueAtTime(0.0001, now + 0.25);
            noiseSrc.connect(bandpass);
            bandpass.connect(gainNoise);
            gainNoise.connect(audioCtx.destination);
            noiseSrc.start();
            noiseSrc.stop(now + 0.25);
            const osc = audioCtx.createOscillator();
            const gainOsc = audioCtx.createGain();
            osc.type = 'triangle';
            osc.frequency.value = 220;
            gainOsc.gain.setValueAtTime(0.28, now);
            gainOsc.gain.exponentialRampToValueAtTime(0.0001, now + 0.12);
            osc.connect(gainOsc);
            gainOsc.connect(audioCtx.destination);
            osc.start();
            osc.stop(now + 0.12);
        }

        function playHiHat(isOpen = false) {
            if(!audioCtx) return;
            const now = audioCtx.currentTime;
            const duration = isOpen ? 0.18 : 0.12;
            const amplitude = isOpen ? 0.32 : 0.45;
            const noiseBuffer = createNoiseBuffer(duration, amplitude);
            if(!noiseBuffer) return;
            const noiseSrc = audioCtx.createBufferSource();
            noiseSrc.buffer = noiseBuffer;
            const highpass = audioCtx.createBiquadFilter();
            highpass.type = 'highpass';
            highpass.frequency.value = 4800;
            highpass.Q.value = 1.1;
            const gainHat = audioCtx.createGain();
            gainHat.gain.setValueAtTime(isOpen ? 0.24 : 0.38, now);
            gainHat.gain.exponentialRampToValueAtTime(0.0001, now + duration);
            noiseSrc.connect(highpass);
            highpass.connect(gainHat);
            gainHat.connect(audioCtx.destination);
            noiseSrc.start();
            noiseSrc.stop(now + duration);
        }

        function playDrumSet(isStrong = true) {
            if(!audioCtx) return;
            if(audioCtx.state === 'suspended') audioCtx.resume().catch(e => console.warn);
            if(isStrong) playSnare();
            else playHiHat(false);
        }

        function initAudio() {
            if(audioCtx) return;
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const silentBuffer = audioCtx.createBuffer(1, 1, 22050);
            const silentSrc = audioCtx.createBufferSource();
            silentSrc.buffer = silentBuffer;
            silentSrc.connect(audioCtx.destination);
            silentSrc.start();
            silentSrc.stop(audioCtx.currentTime + 0.001);
        }

        // 视觉组件：创建拍子按钮（浅色样式）
        function buildBeatVisuals() {
            beatBarContainer.innerHTML = '';
            for(let i = 0; i < 4; i++) {
                const beatBtn = document.createElement('button');
                beatBtn.type = 'button';
                beatBtn.classList.add('beat-icon');
                beatBtn.textContent = (i+1).toString();
                beatBtn.dataset.beatIdx = i;
                // 可选：暂时不绑定点击事件，避免干扰；点击不做任何动作
                beatBarContainer.appendChild(beatBtn);
            }
        }

        function updateSubdivisionVisuals() {
            subDivisionContainer.innerHTML = '';
            const pattern = rhythmPatterns[currentRhythm];
            if(!pattern) return;
            for(let idx = 0; idx < pattern.length; idx++) {
                const dotSpan = document.createElement('div');
                dotSpan.classList.add('sub-unit');
                let label = '';
                if(currentRhythm === 'triplet') label = ['1','2','3'][idx] || (idx+1);
                else if(currentRhythm === 'sixteenth') label = ['e','&','a'][idx] || (idx+1);
                else if(currentRhythm === 'eighth') label = idx===0?'1':'&';
                else if(currentRhythm === 'dotted') label = idx===0?'●':'○';
                else label = (idx+1).toString();
                dotSpan.textContent = label;
                dotSpan.dataset.subIdx = idx;
                subDivisionContainer.appendChild(dotSpan);
            }
        }

        function highlightBeatAndSub(beatIndex, subIdx) {
            const allBeats = document.querySelectorAll('.beat-icon');
            allBeats.forEach(beat => beat.classList.remove('active-beat', 'highlight-sub'));
            if(allBeats[beatIndex]) {
                if(subIdx === 0) allBeats[beatIndex].classList.add('active-beat');
                else allBeats[beatIndex].classList.add('highlight-sub');
            }
            const subUnits = document.querySelectorAll('.sub-unit');
            subUnits.forEach(unit => unit.classList.remove('flash-sub'));
            const pattern = rhythmPatterns[currentRhythm];
            if(pattern && subIdx < subUnits.length) {
                subUnits[subIdx].classList.add('flash-sub');
                setTimeout(() => subUnits[subIdx]?.classList.remove('flash-sub'), 90);
            }
        }

        // 调度引擎
        let activeTimeouts = [];
        function clearAllTimeouts() { activeTimeouts.forEach(clearTimeout); activeTimeouts = []; }

        function stopMetronome() {
            if(!isPlaying) return;
            isPlaying = false;
            clearAllTimeouts();
            document.querySelectorAll('.beat-icon').forEach(b => b.classList.remove('active-beat', 'highlight-sub'));
            document.querySelectorAll('.sub-unit').forEach(s => s.classList.remove('flash-sub'));
        }

        function buildSequence() {
            const beatMs = 60000 / bpm;
            const pattern = rhythmPatterns[currentRhythm];
            if(!pattern) return [];
            const seq = [];
            for(let beat = 0; beat < 4; beat++) {
                for(let sub = 0; sub < pattern.length; sub++) {
                    seq.push({
                        beatIndex: beat,
                        subIndex: sub,
                        delayFromLoopStart: (beat * beatMs) + (pattern[sub] * beatMs),
                        isStrong: sub === 0
                    });
                }
            }
            return seq;
        }

        function startMetronome() {
            if(isPlaying) stopMetronome();
            if(!audioCtx) initAudio();
            if(audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
            const beatMs = 60000 / bpm;
            const fullLoopDuration = beatMs * 4;
            const seq = buildSequence();
            if(seq.length === 0) return;

            document.querySelectorAll('.beat-icon').forEach(b => b.classList.remove('active-beat', 'highlight-sub'));
            document.querySelectorAll('.sub-unit').forEach(s => s.classList.remove('flash-sub'));

            isPlaying = true;
            const startTimestamp = performance.now();

            function scheduleLoop(loopBaseTime) {
                clearAllTimeouts();
                for(const item of seq) {
                    const fireAbsolute = loopBaseTime + item.delayFromLoopStart;
                    const delay = fireAbsolute - performance.now();
                    if(delay < 0) continue;
                    const tid = setTimeout(() => {
                        if(isPlaying) {
                            playDrumSet(item.isStrong);
                            highlightBeatAndSub(item.beatIndex, item.subIndex);
                        }
                    }, delay);
                    activeTimeouts.push(tid);
                }
                const nextLoopStart = loopBaseTime + fullLoopDuration;
                const nextDelay = nextLoopStart - performance.now();
                if(nextDelay > 0 && isPlaying) {
                    const loopTid = setTimeout(() => scheduleLoop(nextLoopStart), nextDelay);
                    activeTimeouts.push(loopTid);
                }
            }
            scheduleLoop(startTimestamp);
        }

        function applyBpmAndRestart() {
            let newBpm = parseInt(tempoInput.value, 10);
            if(isNaN(newBpm)) newBpm = 80;
            newBpm = Math.min(220, Math.max(40, newBpm));
            bpm = newBpm;
            tempoInput.value = bpm;
            if(isPlaying) startMetronome();
        }

        function setRhythm(rhythmId) {
            currentRhythm = rhythmId;
            rhythmBtns.forEach(btn => {
                if(btn.dataset.rhythm === rhythmId) btn.classList.add('active-rhythm');
                else btn.classList.remove('active-rhythm');
            });
            updateSubdivisionVisuals();
            const meta = rhythmMeta[currentRhythm];
            patternDescSpan.innerHTML = `🎯 当前节奏: <strong>${meta.name}</strong> &nbsp;| ${meta.desc} | 🥁 军鼓(重拍) + 镲片(细分)`;
            if(isPlaying) startMetronome();
            else {
                document.querySelectorAll('.beat-icon').forEach(b => b.classList.remove('active-beat', 'highlight-sub'));
                document.querySelectorAll('.sub-unit').forEach(s => s.classList.remove('flash-sub'));
            }
        }

        // Tap Tempo
        let tapTimes = [], tapTimer = null;
        function tapTempo() {
            const now = Date.now();
            tapTimes.push(now);
            if(tapTimes.length > 4) tapTimes.shift();
            if(tapTimes.length >= 2) {
                let sum = 0;
                for(let i=1; i<tapTimes.length; i++) sum += tapTimes[i] - tapTimes[i-1];
                const avg = sum / (tapTimes.length - 1);
                let newBpm = Math.round(60000 / avg);
                newBpm = Math.min(220, Math.max(40, newBpm));
                bpm = newBpm;
                tempoInput.value = bpm;
                if(isPlaying) startMetronome();
            }
            if(tapTimer) clearTimeout(tapTimer);
            tapTimer = setTimeout(() => { tapTimes = []; }, 1500);
        }

        // 事件绑定
        tempoInput.addEventListener('change', applyBpmAndRestart);
        playBtn.addEventListener('click', () => {
            if(!audioCtx) initAudio();
            if(audioCtx) audioCtx.resume();
            startMetronome();
        });
        stopBtn.addEventListener('click', stopMetronome);
        tapBtn.addEventListener('click', tapTempo);
        rhythmBtns.forEach(btn => btn.addEventListener('click', () => setRhythm(btn.dataset.rhythm)));

        buildBeatVisuals();
        setRhythm('straight');
        tempoInput.value = bpm;

        window.addEventListener('beforeunload', () => {
            if(audioCtx) audioCtx.close();
            stopMetronome();
        });
    })();
</script>

<?php $this->need('footer.php'); ?>