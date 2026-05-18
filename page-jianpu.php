<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
/**
 * 简谱Canvas渲染器
 *
 * @package custom
 */
?>
<?php $this->need('header.php'); ?>

<!-- 简谱工具主容器 - 与主题风格一致 -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- 工具栏卡片：调号/拍号/速度设置 -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-3 border-b border-gray-100 flex flex-wrap justify-between items-center gap-3">
            <h2 class="text-xl font-bold text-dark flex items-center gap-2">
                <i class="fas fa-music text-accent"></i> 简谱 Canvas · 完整版
            </h2>
            <div class="flex flex-wrap gap-2 text-xs text-gray-500">
                <span class="bg-gray-200 px-2 py-1 rounded-full">变音 # b n</span>
                <span class="bg-gray-200 px-2 py-1 rounded-full">反复 |: :|</span>
                <span class="bg-gray-200 px-2 py-1 rounded-full">连音 ^</span>
                <span class="bg-gray-200 px-2 py-1 rounded-full">增时线 -</span>
            </div>
        </div>
        <div class="p-4 flex flex-wrap gap-4 items-center">
            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1 rounded-full">
                <label class="text-sm font-medium text-gray-700">调号</label>
                <input type="text" id="keySignature" value="1=C" class="w-16 border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-accent focus:border-accent">
            </div>
            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1 rounded-full">
                <label class="text-sm font-medium text-gray-700">拍号</label>
                <input type="number" id="beatNumerator" value="4" min="1" max="12" step="1" class="w-14 border border-gray-300 rounded-md px-2 py-1 text-sm text-center">
                <span>/</span>
                <input type="number" id="beatDenominator" value="4" min="1" max="8" step="1" class="w-14 border border-gray-300 rounded-md px-2 py-1 text-sm text-center">
            </div>
            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1 rounded-full">
                <label class="text-sm font-medium text-gray-700">速度</label>
                <input type="number" id="tempo" value="120" min="40" max="240" step="5" class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm">
                <span>BPM</span>
            </div>
            <div class="text-xs text-gray-400 ml-auto">增时线 = 延长1拍 | 变音: #升 b降 n还原</div>
        </div>
    </div>

    <!-- 乐谱渲染区域 -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-3 border-b border-gray-100 font-medium text-dark">🎼 渲染输出</div>
        <div class="p-4 overflow-x-auto">
            <canvas id="scoreCanvas" width="900" height="200" class="mx-auto "></canvas>
        </div>
    </div>

    <!-- MIDI 播放与导出 -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-3 border-b border-gray-100 font-medium text-dark">🎵 MIDI 播放器</div>
        <div class="p-4 flex flex-wrap gap-3">
            <button id="playMidiBtn" class="bg-accent hover:bg-accent-dark text-white px-4 py-2 rounded-full text-sm font-medium transition">▶ 播放 MIDI</button>
            <button id="stopMidiBtn" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-full text-sm font-medium transition">⏹ 停止</button>
            <button id="exportMidiBtn" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-full text-sm font-medium transition">🎹 导出 MIDI</button>
            <button id="exportMusicXMLBtn" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-full text-sm font-medium transition">🎼 导出 MusicXML</button>
        </div>
    </div>

    <!-- 简谱文本输入区 -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-3 border-b border-gray-100 font-medium text-dark">✍️ 简谱文本</div>
        <textarea id="inputArea" rows="10" class="w-full p-4 font-mono text-sm border-none focus:ring-0 bg-white text-gray-700" placeholder='示例:
1=C 4/4 120
| #1 b7 n5 2 | 3 4 5 6 |
|: 1 2 3 4 :| n5 6 7 1'"'"' |
| 3 ^ | 4 5 6 | 7 1"'"' ^ | 2"'"' - |
| 5 | ^ 6 7 1"'"' |'></textarea>
    </div>

    <!-- 示例按钮组 -->
    <div class="flex flex-wrap gap-3 mb-6">
        <button id="basicExampleBtn" class="bg-gray-100 hover:bg-gray-200 text-dark px-4 py-2 rounded-full text-sm transition">📝 基础示例</button>
        <button id="accidentalBtn" class="bg-gray-100 hover:bg-gray-200 text-dark px-4 py-2 rounded-full text-sm transition">🎵 变音记号演示</button>
        <button id="repeatBtn" class="bg-gray-100 hover:bg-gray-200 text-dark px-4 py-2 rounded-full text-sm transition">🔁 反复记号演示</button>
        <button id="slurExampleBtn" class="bg-gray-100 hover:bg-gray-200 text-dark px-4 py-2 rounded-full text-sm transition">🎻 跨小节连音演示</button>
        <button id="fullSongBtn" class="bg-gray-100 hover:bg-gray-200 text-dark px-4 py-2 rounded-full text-sm transition">🎶 完整歌曲示例</button>
        <button id="clearBtn" class="bg-gray-100 hover:bg-gray-200 text-dark px-4 py-2 rounded-full text-sm transition">🗑️ 清空</button>
    </div>

    <!-- 使用说明折叠面板 (风格与主题一致) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-3 cursor-pointer flex justify-between items-center" onclick="toggleGuide()">
            <span class="font-medium text-dark flex items-center gap-2"><i class="fas fa-book-open text-accent"></i> 简谱语法参考 & 使用说明</span>
            <i id="guideToggleIcon" class="fas fa-chevron-down text-gray-400 transition-transform"></i>
        </div>
        <div id="guideContent" class="p-6 border-t border-gray-100 hidden">
            <!-- 内容与原来一致，但使用 Tailwind 排版 -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-dark border-l-4 border-accent pl-3 mb-3">🎵 简谱语法规则</h3>
                    <table class="w-full text-sm border-collapse">
                         <thead><tr class="bg-gray-100"><th class="p-2 text-left">语法</th><th class="p-2 text-left">说明</th><th class="p-2 text-left">示例</th></tr></thead>
                         <tbody>
                             <tr><td class="p-2"><code>1 2 3 4 5 6 7</code></td><td class="p-2">基本音级</td><td class="p-2"><code>1 2 3 | 4 5 6 |</code></td></tr>
                             <tr><td class="p-2"><code>1' 2'' 3'''</code></td><td class="p-2">高音（撇号）</td><td class="p-2"><code>1' 2'' 3''' |</code></td></tr>
                             <tr><td class="p-2"><code>1, 2,, 3,,,</code></td><td class="p-2">低音（逗号）</td><td class="p-2"><code>1, 2,, 3,,, |</code></td></tr>
                             <tr><td class="p-2"><code>#1 b2 n3</code></td><td class="p-2">变音记号</td><td class="p-2"><code>#1 b7 n5 |</code></td></tr>
                             <tr><td class="p-2"><code>-</code></td><td class="p-2">增时线（延长1拍）</td><td class="p-2"><code>1 - - |</code></td></tr>
                             <tr><td class="p-2"><code>=</code></td><td class="p-2">双线（1/4拍）</td><td class="p-2"><code>5=6= |</code></td></tr>
                             <tr><td class="p-2"><code>|</code></td><td class="p-2">小节线</td><td class="p-2"><code>| 1 2 3 4 |</code></td></tr>
                             <tr><td class="p-2"><code>|:  :|</code></td><td class="p-2">反复记号</td><td class="p-2"><code>|: 1 2 :| 3 4 |</code></td></tr>
                             <tr><td class="p-2"><code>^</code></td><td class="p-2">连音线</td><td class="p-2"><code>1^2 3 ^ 4</code></td></tr>
                         </tbody>
                    </table>
                </div>
                <div>
                    <h3 class="font-bold text-dark border-l-4 border-accent pl-3 mb-3">⚙️ 元数据设置</h3>
                    <table class="w-full text-sm border-collapse"><thead><tr class="bg-gray-100"><th class="p-2 text-left">语法</th><th class="p-2 text-left">说明</th></tr></thead>
                    <tbody>
                        <tr><td class="p-2"><code>title: 我的祖国</code></td><td class="p-2">设置曲谱标题</td></tr>
                        <tr><td class="p-2"><code>1=C / 1=G / 1=F</code></td><td class="p-2">设置调号</td></tr>
                        <tr><td class="p-2"><code>4/4 / 3/4 / 6/8</code></td><td class="p-2">设置拍号</td></tr>
                        <tr><td class="p-2"><code>120 / 80 / 140</code></td><td class="p-2">设置速度(BPM)</td></tr>
                    </tbody></table>
                </div>
            </div>
            <div class="mt-4 bg-gray-50 p-4 rounded-xl">
                <div class="font-bold mb-2">📝 完整示例</div>
                <pre class="text-xs bg-gray-800 text-gray-100 p-3 rounded overflow-x-auto">title: 我的祖国
1=C 4/4 120
| 1 2 3 5 | 6 - 5 3 | 2 - 1 - |
|: #1 2 b3 4 :| 5 6 7 1' | 1' - - - |</pre>
            </div>
            <div class="mt-4 text-xs text-gray-500 border-t pt-4">
                💡 音符之间用空格分隔；播放 MIDI 前请点击页面任意位置激活音频；MusicXML 可用 MuseScore 打开。
            </div>
        </div>
    </div>
</div>

<!-- 引入简谱渲染相关 JS 模块 -->
<script src="<?php $this->options->themeUrl('assets/js/musicxml-exporter.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('assets/js/midi-exporter.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('assets/js/jianpu-renderer.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== MIDI 播放逻辑（与原代码一致） ==========
    let isPlaying = false, audioContext = null, scheduledTimeouts = [], currentGainNodes = [];
    function midiToFrequency(n) { return 440 * Math.pow(2, (n-69)/12); }
    function initAudioContext() { if (!audioContext) audioContext = new (window.AudioContext||window.webkitAudioContext)(); return audioContext; }
    function playNote(pitch, vel, durMs) {
        if (!audioContext) return;
        const osc = audioContext.createOscillator(), gain = audioContext.createGain();
        osc.connect(gain); gain.connect(audioContext.destination);
        osc.type = 'triangle'; osc.frequency.value = midiToFrequency(pitch);
        const now = audioContext.currentTime;
        gain.gain.setValueAtTime(0, now);
        gain.gain.linearRampToValueAtTime((vel/127)*0.4, now+0.005);
        gain.gain.exponentialRampToValueAtTime(0.0001, now+durMs/1000);
        osc.start(); currentGainNodes.push(gain);
        const timeout = setTimeout(() => { try { osc.stop(); osc.disconnect(); gain.disconnect(); } catch(e){} }, durMs);
        scheduledTimeouts.push(timeout);
    }
    function stopAll() {
        isPlaying = false;
        scheduledTimeouts.forEach(clearTimeout);
        scheduledTimeouts = [];
        currentGainNodes.forEach(g => { try { g.disconnect(); } catch(e) {} });
        currentGainNodes = [];
    }
    function buildTimeline(events, tempo) {
        const msPerTick = (60/tempo)*1000/480;
        return events.map(ev => ({
            pitch: ev.pitch,
            startTime: ev.startTick * msPerTick,
            durationMs: ev.durationTicks * msPerTick,
            velocity: 80
        })).sort((a,b) => a.startTime - b.startTime);
    }
    async function playMidiData(events, tempo) {
        stopAll();
        if (!events.length) return;
        const ctx = initAudioContext();
        if (ctx.state === 'suspended') { alert('请先点击页面任意位置激活音频'); return; }
        const tl = buildTimeline(events, tempo);
        isPlaying = true;
        tl.forEach(n => scheduledTimeouts.push(setTimeout(() => { if(isPlaying) playNote(n.pitch, n.velocity, n.durationMs); }, n.startTime)));
        scheduledTimeouts.push(setTimeout(() => { isPlaying = false; }, tl[tl.length-1].startTime + tl[tl.length-1].durationMs + 500));
    }
    document.getElementById('playMidiBtn').addEventListener('click', async function() {
        const d = window.getScoreData();
        if (!d || !d.rowsMeasures) { alert('没有乐谱数据'); return; }
        const exporter = new MidiExporter();
        const keyOffset = exporter.getKeyOffset(d.keySig);
        const { events } = exporter.convertToMidiEvents(d.rowsMeasures, d.beatsPerBar, d.beatUnit, d.tempo, keyOffset);
        if (!events.length) { alert('没有音符事件'); return; }
        await playMidiData(events, d.tempo);
    });
    document.getElementById('stopMidiBtn').addEventListener('click', stopAll);
    document.getElementById('exportMidiBtn').addEventListener('click', function() {
        const d = window.getScoreData();
        if (!d || !d.rowsMeasures) { alert('没有乐谱数据'); return; }
        const exporter = new MidiExporter();
        const fileName = (d.title ? d.title : 'jianpu') + '.mid';
        exporter.exportMidi(d.rowsMeasures, d.beatsPerBar, d.beatUnit, d.tempo, d.keySig, fileName);
    });
    document.getElementById('exportMusicXMLBtn').addEventListener('click', function() {
        const d = window.getScoreData();
        if (!d || !d.rowsMeasures) { alert('没有乐谱数据'); return; }
        const exporter = new MusicXMLExporter();
        exporter.exportToFile(d.rowsMeasures, d.beatsPerBar, d.beatUnit, d.tempo, d.keySig, d.title || 'jianpu');
    });
});
// 使用说明折叠切换
function toggleGuide() {
    const content = document.getElementById('guideContent');
    const icon = document.getElementById('guideToggleIcon');
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>

<?php $this->need('footer.php'); ?>