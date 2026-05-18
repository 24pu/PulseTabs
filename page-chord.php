<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
/**
 * 吉他和弦图谱
 *
 * @package custom
 */
?>
<?php $this->need('header.php'); ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- 左侧：和弦库卡片网格 -->
        <div class="lg:w-80 xl:w-96 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto">
                <div class="bg-gray-50 px-5 py-3 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-dark flex items-center gap-2">
                        <i class="fas fa-guitar text-accent"></i> 吉他和弦库
                    </h2>
                </div>
                <div id="chordCategories" class="p-4 space-y-6"></div>
            </div>
        </div>

        <!-- 右侧：主面板 -->
        <div class="flex-1">
            <!-- 和弦显示卡片 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gray-50 px-5 py-3 border-b border-gray-100 flex justify-between items-center flex-wrap gap-2">
                    <span class="text-2xl font-bold text-dark" id="currentChordName">C</span>
                    <span class="text-sm bg-accent/10 text-accent px-3 py-1 rounded-full" id="currentChordType">大三和弦</span>
                </div>
                <div class="p-5">
                    <div class="bg-amber-50 rounded-xl p-3">
                        <canvas id="guitarCanvas" width="600" height="200" class="w-full h-auto mx-auto shadow-sm rounded bg-white"></canvas>
                        <div class="text-center text-xs text-gray-500 mt-3 flex items-center justify-center gap-2">
                            <i class="fas fa-guitar"></i> 6弦(左) → 1弦(右) | 数字=品格 | 0=空弦 | ✕=不弹
                        </div>
                    </div>
                </div>
            </div>

            <!-- 反查卡片 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-5 py-3 border-b border-gray-100">
                    <h3 class="font-medium text-dark flex items-center gap-2">
                        <i class="fas fa-search text-accent"></i> 品格反查 (6弦 → 1弦)
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" id="fingerInput" placeholder="示例: 0 3 2 0 1 0  或 -1 0 2 2 1 0" value="0 3 2 0 1 0" class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-accent focus:border-accent">
                        <button id="searchBtn" class="bg-accent hover:bg-accent-dark text-white px-5 py-2 rounded-full transition flex items-center justify-center gap-2">
                            <i class="fas fa-chart-simple"></i> 反查和弦
                        </button>
                    </div>
                    <div id="searchResult" class="bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-600 border border-gray-100">
                        💡 输入品格后点击查询，自动匹配和弦
                    </div>
                    <div class="text-xs text-gray-400">* 支持空格或逗号分隔，x 或 -1 表示不弹</div>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-gray-400 border-t border-gray-100 pt-4">
                🎸 内置数据 | 三和弦 / 七和弦 / 九和弦 / 增减和弦 / 挂留和弦 | 点击左侧和弦显示指板图
            </div>
        </div>
    </div>
</div>

<script>
    // ==================== 和弦数据库 (与原版完全一致) ====================
    const chordLibrary = [
        // 大三和弦
        { name: "C", typeDesc: "大三和弦", category: "三和弦", finger: [0,3,2,0,1,0] },
        { name: "D", typeDesc: "大三和弦", category: "三和弦", finger: [-1,0,0,2,3,2] },
        { name: "E", typeDesc: "大三和弦", category: "三和弦", finger: [0,2,2,1,0,0] },
        { name: "F", typeDesc: "大三和弦", category: "三和弦", finger: [1,3,3,2,1,1] },
        { name: "G", typeDesc: "大三和弦", category: "三和弦", finger: [3,2,0,0,0,3] },
        { name: "A", typeDesc: "大三和弦", category: "三和弦", finger: [0,0,2,2,2,0] },
        { name: "B", typeDesc: "大三和弦", category: "三和弦", finger: [2,2,4,4,4,2] },
        // 小三和弦
        { name: "Cm", typeDesc: "小三和弦", category: "三和弦", finger: [-1,3,5,5,4,3] },
        { name: "Dm", typeDesc: "小三和弦", category: "三和弦", finger: [-1,0,0,2,3,1] },
        { name: "Em", typeDesc: "小三和弦", category: "三和弦", finger: [0,2,2,0,0,0] },
        { name: "Fm", typeDesc: "小三和弦", category: "三和弦", finger: [1,3,3,1,1,1] },
        { name: "Gm", typeDesc: "小三和弦", category: "三和弦", finger: [3,5,5,3,3,3] },
        { name: "Am", typeDesc: "小三和弦", category: "三和弦", finger: [-1,0,2,2,1,0] },
        { name: "Bm", typeDesc: "小三和弦", category: "三和弦", finger: [-1,2,2,4,4,2] },
        // 属七和弦
        { name: "C7", typeDesc: "属七和弦", category: "七和弦", finger: [0,3,2,3,1,0] },
        { name: "D7", typeDesc: "属七和弦", category: "七和弦", finger: [-1,0,0,2,1,2] },
        { name: "E7", typeDesc: "属七和弦", category: "七和弦", finger: [0,2,0,1,0,0] },
        { name: "F7", typeDesc: "属七和弦", category: "七和弦", finger: [1,3,1,2,1,1] },
        { name: "G7", typeDesc: "属七和弦", category: "七和弦", finger: [3,2,0,0,0,1] },
        { name: "A7", typeDesc: "属七和弦", category: "七和弦", finger: [-1,0,2,0,2,0] },
        { name: "B7", typeDesc: "属七和弦", category: "七和弦", finger: [-1,2,1,2,0,2] },
        // 大七和弦
        { name: "Cmaj7", typeDesc: "大七和弦", category: "七和弦", finger: [0,3,2,0,0,0] },
        { name: "Dmaj7", typeDesc: "大七和弦", category: "七和弦", finger: [-1,0,0,2,2,2] },
        { name: "Emaj7", typeDesc: "大七和弦", category: "七和弦", finger: [0,2,2,4,4,4] },
        { name: "Fmaj7", typeDesc: "大七和弦", category: "七和弦", finger: [1,3,2,2,1,1] },
        { name: "Gmaj7", typeDesc: "大七和弦", category: "七和弦", finger: [3,2,0,0,0,2] },
        { name: "Amaj7", typeDesc: "大七和弦", category: "七和弦", finger: [-1,0,2,1,2,0] },
        { name: "Bmaj7", typeDesc: "大七和弦", category: "七和弦", finger: [-1,2,2,4,4,4] },
        // 小七和弦
        { name: "Cm7", typeDesc: "小七和弦", category: "七和弦", finger: [-1,3,5,3,4,3] },
        { name: "Dm7", typeDesc: "小七和弦", category: "七和弦", finger: [-1,0,0,2,1,1] },
        { name: "Em7", typeDesc: "小七和弦", category: "七和弦", finger: [0,2,0,0,0,0] },
        { name: "Fm7", typeDesc: "小七和弦", category: "七和弦", finger: [1,3,1,1,1,1] },
        { name: "Gm7", typeDesc: "小七和弦", category: "七和弦", finger: [3,5,3,3,3,3] },
        { name: "Am7", typeDesc: "小七和弦", category: "七和弦", finger: [-1,0,2,0,1,0] },
        // 九和弦
        { name: "C9", typeDesc: "属九和弦", category: "九和弦", finger: [-1,3,2,3,3,3] },
        { name: "D9", typeDesc: "属九和弦", category: "九和弦", finger: [-1,0,0,2,2,2] },
        { name: "G9", typeDesc: "属九和弦", category: "九和弦", finger: [3,2,0,2,0,2] },
        { name: "A9", typeDesc: "属九和弦", category: "九和弦", finger: [-1,0,2,4,2,4] },
        { name: "Cmaj9", typeDesc: "大九和弦", category: "九和弦", finger: [-1,3,2,0,0,2] },
        { name: "Gmaj9", typeDesc: "大九和弦", category: "九和弦", finger: [3,2,0,2,2,2] },
        { name: "Cm9", typeDesc: "小九和弦", category: "九和弦", finger: [-1,3,5,3,3,3] },
        { name: "Dm9", typeDesc: "小九和弦", category: "九和弦", finger: [-1,0,0,2,1,3] },
        // 增减和弦
        { name: "Caug", typeDesc: "增三和弦", category: "增减和弦", finger: [0,3,2,1,1,0] },
        { name: "Gaug", typeDesc: "增三和弦", category: "增减和弦", finger: [3,2,1,0,1,3] },
        { name: "Eaug", typeDesc: "增三和弦", category: "增减和弦", finger: [0,3,2,1,1,0] },
        { name: "Cdim", typeDesc: "减三和弦", category: "增减和弦", finger: [-1,3,4,2,4,2] },
        { name: "Gdim", typeDesc: "减三和弦", category: "增减和弦", finger: [-1,3,4,2,4,2] },
        { name: "Adim", typeDesc: "减三和弦", category: "增减和弦", finger: [-1,2,3,1,3,1] },
        { name: "Cdim7", typeDesc: "减七和弦", category: "增减和弦", finger: [-1,4,3,4,3,4] },
        { name: "Gdim7", typeDesc: "减七和弦", category: "增减和弦", finger: [-1,4,3,4,3,4] },
        // 挂留和弦
        { name: "Csus2", typeDesc: "挂二和弦", category: "挂留和弦", finger: [-1,3,0,0,1,0] },
        { name: "Csus4", typeDesc: "挂四和弦", category: "挂留和弦", finger: [-1,3,3,0,1,1] },
        { name: "Dsus2", typeDesc: "挂二和弦", category: "挂留和弦", finger: [-1,0,0,2,3,0] },
        { name: "Dsus4", typeDesc: "挂四和弦", category: "挂留和弦", finger: [-1,0,0,2,3,3] },
        { name: "Esus4", typeDesc: "挂四和弦", category: "挂留和弦", finger: [0,2,2,2,0,0] },
        { name: "Gsus2", typeDesc: "挂二和弦", category: "挂留和弦", finger: [3,0,0,0,1,3] },
        { name: "Gsus4", typeDesc: "挂四和弦", category: "挂留和弦", finger: [3,2,0,0,1,3] },
        { name: "Asus2", typeDesc: "挂二和弦", category: "挂留和弦", finger: [-1,0,2,2,0,0] },
        { name: "Asus4", typeDesc: "挂四和弦", category: "挂留和弦", finger: [-1,0,2,2,3,0] },
        { name: "Fsus4", typeDesc: "挂四和弦", category: "挂留和弦", finger: [1,3,3,1,1,1] }
    ];

    // 构建左侧分类网格
    function buildChordGrid() {
        const categories = new Map();
        for (const chord of chordLibrary) {
            if (!categories.has(chord.category)) categories.set(chord.category, []);
            categories.get(chord.category).push(chord);
        }
        const container = document.getElementById("chordCategories");
        container.innerHTML = "";
        for (const [catName, chords] of categories.entries()) {
            const catDiv = document.createElement("div");
            catDiv.className = "space-y-2";
            catDiv.innerHTML = `<div class="text-sm font-semibold text-dark border-l-3 border-accent pl-2">${catName}</div><div class="grid grid-cols-2 gap-2">`;
            chords.forEach(chord => {
                const card = document.createElement("div");
                card.className = "bg-white border border-gray-200 rounded-xl p-2 text-center cursor-pointer hover:bg-gray-50 hover:border-accent transition";
                card.setAttribute("data-name", chord.name);
                card.innerHTML = `<div class="font-bold text-dark">${chord.name}</div><div class="text-xs text-gray-500">${chord.typeDesc}</div>`;
                card.addEventListener("click", () => selectChord(chord));
                catDiv.querySelector(".grid").appendChild(card);
            });
            container.appendChild(catDiv);
        }
    }

    // 绘制吉他和弦图
    function drawChordOnCanvas(fingerArray) {
        const canvas = document.getElementById("guitarCanvas");
        const ctx = canvas.getContext("2d");
        const width = canvas.width, height = canvas.height;
        const leftMargin = 44, rightMargin = 20;
        const topMargin = 22, bottomMargin = 22;
        const fretStartX = leftMargin;
        const fretEndX = width - rightMargin;
        const fretRangeWidth = fretEndX - fretStartX;
        const maxFretShow = 5;
        const fretCount = maxFretShow;
        const cellWidth = fretRangeWidth / fretCount;
        const stringCount = 6;
        const availableHeight = height - topMargin - bottomMargin;
        const stringSpacing = availableHeight / (stringCount - 1);
        let yPos = [];
        for (let i = 0; i < stringCount; i++) {
            yPos.push(topMargin + (stringCount - 1 - i) * stringSpacing);
        }
        ctx.clearRect(0, 0, width, height);
        ctx.save();
        ctx.strokeStyle = "#9c8468";
        ctx.lineWidth = 1.2;
        // 品柱
        for (let f = 0; f <= fretCount; f++) {
            const x = fretStartX + f * cellWidth;
            ctx.beginPath();
            ctx.moveTo(x, topMargin - 3);
            ctx.lineTo(x, height - bottomMargin + 3);
            ctx.stroke();
            if (f === 0) {
                ctx.lineWidth = 2.5;
                ctx.strokeStyle = "#b49470";
                ctx.stroke();
                ctx.lineWidth = 1.2;
                ctx.strokeStyle = "#9c8468";
            }
            if (f > 0 && f <= fretCount) {
                ctx.font = "12px system-ui";
                ctx.fillStyle = "#aa8b62";
                ctx.fillText(`${f}品`, x + cellWidth/2 - 8, height - bottomMargin + 12);
            }
        }
        // 琴弦
        for (let s = 0; s < stringCount; s++) {
            ctx.beginPath();
            ctx.moveTo(leftMargin - 5, yPos[s]);
            ctx.lineTo(width - rightMargin + 5, yPos[s]);
            ctx.stroke();
            ctx.font = "bold 11px monospace";
            ctx.fillStyle = "#705c42";
            ctx.fillText(`${6 - s}弦`, leftMargin - 22, yPos[s] + 3);
        }
        // 按弦点
        for (let i = 0; i < fingerArray.length; i++) {
            const fret = fingerArray[i];
            const y = yPos[i];
            if (fret === -1) {
                ctx.font = "bold 14px system-ui";
                ctx.fillStyle = "#bc8f6b";
                ctx.fillText("✕", leftMargin - 16, y + 5);
                continue;
            }
            if (fret === 0) {
                ctx.font = "bold 14px monospace";
                ctx.fillStyle = "#6495a3";
                ctx.fillText("0", leftMargin - 16, y + 5);
                continue;
            }
            if (fret > 0 && fret <= maxFretShow) {
                const leftFretX = fretStartX + (fret - 1) * cellWidth;
                const rightFretX = fretStartX + fret * cellWidth;
                const centerX = (leftFretX + rightFretX) / 2;
                ctx.beginPath();
                ctx.arc(centerX, y, 7, 0, 2 * Math.PI);
                ctx.fillStyle = "#b27d46";
                ctx.fill();
                ctx.fillStyle = "white";
                ctx.font = "bold 12px system-ui";
                ctx.fillText(fret.toString(), centerX - 4, y + 4);
                ctx.beginPath();
                ctx.arc(centerX, y, 7, 0, 2 * Math.PI);
                ctx.strokeStyle = "#dbb47a";
                ctx.lineWidth = 1.2;
                ctx.stroke();
            } else {
                const warnX = fretStartX + fretCount * cellWidth - 6;
                ctx.font = "italic 10px sans-serif";
                ctx.fillStyle = "#c96f3e";
                ctx.fillText(`${fret}品`, warnX, y - 5);
            }
        }
        ctx.restore();
        ctx.font = "10px system-ui";
        ctx.fillStyle = "#b5956e";
        ctx.fillText("← 低音弦 (6弦)      高音弦 (1弦) →", leftMargin + 40, topMargin - 3);
    }

    let currentChordObj = null;
    function selectChord(chord) {
        currentChordObj = chord;
        document.getElementById("currentChordName").innerText = chord.name;
        document.getElementById("currentChordType").innerText = chord.typeDesc;
        drawChordOnCanvas(chord.finger);
        // 高亮卡片
        document.querySelectorAll("#chordCategories .grid > div").forEach(card => {
            const nameSpan = card.querySelector(".font-bold");
            if (nameSpan && nameSpan.innerText === chord.name) {
                card.classList.add("border-accent", "bg-accent/5");
                card.classList.remove("border-gray-200");
            } else {
                card.classList.remove("border-accent", "bg-accent/5");
                card.classList.add("border-gray-200");
            }
        });
    }

    function findChordByFingerprint(arr) {
        if (!arr || arr.length !== 6) return null;
        return chordLibrary.find(chord => chord.finger.every((v, i) => v === arr[i]));
    }

    function parseFingerprint(inputStr) {
        let cleaned = inputStr.trim().replace(/，/g,',').replace(/,/g,' ');
        let parts = cleaned.split(/\s+/);
        if (parts.length !== 6) return null;
        let result = [];
        for (let p of parts) {
            let v = p.trim().toLowerCase();
            if (v === "x") v = "-1";
            let num = parseInt(v, 10);
            if (isNaN(num) || num < -1) return null;
            result.push(num);
        }
        return result;
    }

    function reverseSearch() {
        const input = document.getElementById("fingerInput").value;
        const parsed = parseFingerprint(input);
        const resultDiv = document.getElementById("searchResult");
        if (!parsed) {
            resultDiv.innerHTML = "❌ 格式错误，需输入6个数字（空格或逗号分隔），-1 或 x 表示不弹";
            return;
        }
        const matched = findChordByFingerprint(parsed);
        if (matched) {
            resultDiv.innerHTML = `✅ 精确匹配：<span class="font-bold text-accent">${matched.name}</span> (${matched.typeDesc}) 已自动加载指板图`;
            selectChord(matched);
        } else {
            resultDiv.innerHTML = `🔍 未找到匹配和弦：[${parsed.join(", ")}]<br>可以尝试其他指法或点击左侧和弦库浏览。`;
            drawChordOnCanvas(parsed);
            document.getElementById("currentChordName").innerText = "自定义指法";
            document.getElementById("currentChordType").innerText = "未收录";
            document.querySelectorAll("#chordCategories .grid > div").forEach(card => {
                card.classList.remove("border-accent", "bg-accent/5");
                card.classList.add("border-gray-200");
            });
        }
    }

    // 初始化
    function init() {
        buildChordGrid();
        const defaultChord = chordLibrary.find(c => c.name === "C") || chordLibrary[0];
        if (defaultChord) selectChord(defaultChord);
        document.getElementById("searchBtn").addEventListener("click", reverseSearch);
        document.getElementById("fingerInput").addEventListener("keypress", (e) => {
            if (e.key === "Enter") reverseSearch();
        });
    }
    init();
</script>

<?php $this->need('footer.php'); ?>