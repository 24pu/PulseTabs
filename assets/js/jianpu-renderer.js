// assets/js/jianpu-renderer.js
// 简谱渲染引擎 - 支持均匀间距、跨小节连音、变音记号、反复记号
(function() {
    const textarea = document.getElementById('inputArea');
    const canvas = document.getElementById('scoreCanvas');
    const container = document.getElementById('canvasContainer');
    const beatNumeratorInput = document.getElementById('beatNumerator');
    const beatDenominatorInput = document.getElementById('beatDenominator');
    const keySignatureInput = document.getElementById('keySignature');
    const tempoInput = document.getElementById('tempo');
    let ctx = canvas.getContext('2d');

    let globalKeySignature = '1=C';
    let globalTempo = 120;
    let globalBeatsPerBar = 4;
    let globalBeatUnit = 4;
    let globalTitle = '';
    let currentRowsMeasures = null;
    let globalCopyright = '24pu.com';

    // 间距常量
    // 在文件顶部，与其他常量一起添加
    const NORMAL_BARLINE_WIDTH = 1.0;   // 普通小节线粗细，可自行修改
    const NOTE_SPACING = 6;       // 音符之间的水平间距（也用于小节线与音符）
    const BARLINE_WIDTH = 2;      // 小节线本身宽度（用于布局，实际绘制线宽可能不同）
    const MEASURE_GAP = NOTE_SPACING * 2 + BARLINE_WIDTH; // 一个小节到下一小节的额外总间距

    function getTimeSignature() {
        let numerator = parseInt(beatNumeratorInput.value, 10);
        let denominator = parseInt(beatDenominatorInput.value, 10);
        if (isNaN(numerator) || numerator < 1) numerator = 4;
        if (numerator > 16) numerator = 16;
        if (isNaN(denominator) || denominator < 1) denominator = 4;
        if (denominator > 8) denominator = 4;
        globalBeatsPerBar = numerator;
        globalBeatUnit = denominator;
        return { beatsPerBar: numerator, beatUnit: denominator };
    }

    function getGlobalSettings() {
        globalKeySignature = keySignatureInput.value || '1=C';
        globalTempo = parseInt(tempoInput.value, 10) || 120;
        getTimeSignature();
    }

    function parseMetadataLine(line) {
        if (!line) return null;
        const parts = line.trim().split(/\s+/);
        let key = null, timeSig = null, tempo = null;
        for (let part of parts) {
            if (part.match(/^[1-7b#]?=[A-Ga-g]?$/i) || part.match(/^[1-7]=[A-Ga-g]$/i) || part.match(/^[1-7][b#]?=[A-Ga-g]?$/)) {
                key = part;
            }
            if (part.match(/^\d+\/\d+$/)) timeSig = part;
            if (part.match(/^\d+$/) && parseInt(part) >= 40 && parseInt(part) <= 240) tempo = parseInt(part);
        }
        return { key, timeSig, tempo };
    }

    function parseTitleLine(line) {
        if (!line) return null;
        const trimmed = line.trim();
        if (trimmed.toLowerCase().startsWith('title:')) {
            return trimmed.substring(6).trim();
        }
        return null;
    }

    function updateUIFromMetadata(metadata) {
        if (metadata.key) { keySignatureInput.value = metadata.key; globalKeySignature = metadata.key; }
        if (metadata.timeSig) {
            const [num, den] = metadata.timeSig.split('/');
            beatNumeratorInput.value = parseInt(num);
            beatDenominatorInput.value = parseInt(den);
            globalBeatsPerBar = parseInt(num);
            globalBeatUnit = parseInt(den);
        }
        if (metadata.tempo) { tempoInput.value = metadata.tempo; globalTempo = metadata.tempo; }
    }

    function parseLineToMeasures(line) {
        if (!line.trim()) return [];
        let measures = [];
        if (!line.includes('|')) {
            if (line.trim()) measures.push({ tokens: line.trim().split(/\s+/).filter(t => t.length > 0), repeatStart: false, repeatEnd: false });
        } else {
            const parts = line.split('|');
            for (let i = 0; i < parts.length; i++) {
                let p = parts[i];
                let trimmed = p.trim();
                let hasRepeatStart = (trimmed.startsWith(':') && trimmed.length > 1);
                let hasRepeatEnd = (trimmed.endsWith(':') && trimmed.length > 1);
                if (hasRepeatStart) trimmed = trimmed.substring(1).trim();
                if (hasRepeatEnd && !hasRepeatStart) trimmed = trimmed.substring(0, trimmed.length - 1).trim();
                if (hasRepeatStart && hasRepeatEnd && trimmed.length === 0) trimmed = '';
                if (trimmed !== "") {
                    measures.push({ tokens: trimmed.split(/\s+/).filter(t => t.length > 0), repeatStart: hasRepeatStart, repeatEnd: hasRepeatEnd });
                } else if (hasRepeatStart || hasRepeatEnd) {
                    measures.push({ tokens: [], repeatStart: hasRepeatStart, repeatEnd: hasRepeatEnd });
                }
            }
        }
        return measures;
    }

    function parseNoteWithAccidental(token) {
        const match = token.match(/^([#bn])?([1-7][',]*)([=-]?)([HPS]?)$/);
        if (match) {
            return {
                type: 'note',
                pitch: match[2],
                accidental: match[1] || '',
                duration: match[3] || '',
                techSuffix: match[4] || null,
                rawToken: token
            };
        }
        return null;
    }

    function isSlurMarker(token) { return token === '^' || token === 'H' || token === 'P' || token === 'S'; }
    function getSlurLetter(token) { if (token === '^') return '^'; if (token === 'H' || token === 'P' || token === 'S') return token; return null; }

    function parseMeasureToUnits(tokens) {
        let units = [];
        let i = 0;
        while (i < tokens.length) {
            let token = tokens[i];
            if (token === '-') {
                units.push({ type: 'augment', augmentCount: 1, originalToken: '-' });
                i++;
                continue;
            }

            if (isSlurMarker(token)) {
                const slurLetter = getSlurLetter(token);
                if (i + 1 < tokens.length) {
                    const nextToken = tokens[i + 1];
                    const nextNote = parseNoteWithAccidental(nextToken);
                    if (nextNote) {
                        if (units.length > 0 && units[units.length - 1].type === 'note') {
                            const prevNote = units[units.length - 1];
                            units.pop();
                            units.push({
                                type: 'slur',
                                note1: { pitch: prevNote.pitch, accidental: prevNote.accidental || '', duration: prevNote.duration || '' },
                                note2: { pitch: nextNote.pitch, accidental: nextNote.accidental || '', duration: nextNote.duration || '' },
                                letter: slurLetter,
                                rawToken: token + nextToken
                            });
                            i += 2;
                            continue;
                        } else {
                            units.push({ type: 'slur_marker', letter: slurLetter, pending: true, rawToken: token });
                            i++;
                            continue;
                        }
                    }
                } else {
                    units.push({ type: 'slur_marker', letter: slurLetter, pending: true, rawToken: token });
                    i++;
                    continue;
                }
            }

            if (token.includes('^') && !isSlurMarker(token)) {
                const parts = token.split('^');
                if (parts.length === 2) {
                    const leftMatch = parts[0].match(/^([#bn])?([1-7][',]*)([=-]?)$/);
                    const rightMatch = parts[1].match(/^([#bn])?([1-7][',]*)([=-]?)$/);
                    if (leftMatch && rightMatch) {
                        units.push({
                            type: 'slur',
                            note1: { pitch: leftMatch[2], accidental: leftMatch[1] || '', duration: leftMatch[3] || '' },
                            note2: { pitch: rightMatch[2], accidental: rightMatch[1] || '', duration: rightMatch[3] || '' },
                            letter: '^',
                            rawToken: token
                        });
                        i++;
                        continue;
                    }
                }
            }

            const noteMatch = parseNoteWithAccidental(token);
            if (noteMatch) {
                units.push(noteMatch);
            } else {
                units.push({ type: 'note', pitch: '?', accidental: '', duration: '', techSuffix: null, rawToken: token });
            }
            i++;
        }
        return units;
    }

    // 后处理跨小节连音
    function postProcessSlurs(rowsMeasures) {
        const crossSlurList = [];
        let pendingSlur = null;

        for (let rowIdx = 0; rowIdx < rowsMeasures.length; rowIdx++) {
            const measures = rowsMeasures[rowIdx];
            if (!measures) continue;
            for (let measureIdx = 0; measureIdx < measures.length; measureIdx++) {
                const measure = measures[measureIdx];
                const units = measure.units;
                for (let unitIdx = 0; unitIdx < units.length; unitIdx++) {
                    const unit = units[unitIdx];
                    if (unit.type === 'slur_marker' && unit.pending) {
                        pendingSlur = { rowIdx, measureIdx, unitIdx, letter: unit.letter };
                        units.splice(unitIdx, 1);
                        unitIdx--;
                    } else if (unit.type === 'note' && pendingSlur !== null) {
                        let prevNote = null;
                        let prevRowIdx = pendingSlur.rowIdx;
                        let prevMeasureIdx = pendingSlur.measureIdx;
                        let prevUnitIdx = pendingSlur.unitIdx - 1;
                        if (prevUnitIdx >= 0 && rowsMeasures[prevRowIdx] && rowsMeasures[prevRowIdx][prevMeasureIdx]) {
                            prevNote = rowsMeasures[prevRowIdx][prevMeasureIdx].units[prevUnitIdx];
                        } else {
                            let found = false;
                            for (let r = prevRowIdx; r >= 0 && !found; r--) {
                                const mList = rowsMeasures[r];
                                if (!mList) continue;
                                const startM = (r === prevRowIdx ? prevMeasureIdx - 1 : mList.length - 1);
                                for (let m = startM; m >= 0 && !found; m--) {
                                    const ulist = mList[m].units;
                                    for (let u = ulist.length - 1; u >= 0; u--) {
                                        if (ulist[u].type === 'note') {
                                            prevNote = ulist[u];
                                            prevRowIdx = r;
                                            prevMeasureIdx = m;
                                            prevUnitIdx = u;
                                            found = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if (prevNote && prevNote.type === 'note') {
                            prevNote.crossSlurTo = { targetRowIdx: rowIdx, targetMeasureIdx: measureIdx, targetUnitIdx: unitIdx, letter: pendingSlur.letter };
                            unit.crossSlurFrom = { sourceRowIdx: prevRowIdx, sourceMeasureIdx: prevMeasureIdx, sourceUnitIdx: prevUnitIdx, letter: pendingSlur.letter };
                            crossSlurList.push({
                                sourcePos: { rowIdx: prevRowIdx, measureIdx: prevMeasureIdx, unitIdx: prevUnitIdx },
                                targetPos: { rowIdx, measureIdx, unitIdx },
                                letter: pendingSlur.letter
                            });
                            pendingSlur = null;
                        } else {
                            pendingSlur = null;
                        }
                    }
                }
            }
        }
        return crossSlurList;
    }

    function getNoteDisplay(pitchStr) {
        if (!pitchStr || pitchStr === '?') return { digit: '?', upDots: 0, downDots: 0 };
        let digit = pitchStr[0];
        let upDots = (pitchStr.match(/'/g) || []).length;
        let downDots = (pitchStr.match(/,/g) || []).length;
        return { digit, upDots, downDots };
    }

    function getAccidentalSymbol(acc) {
        if (acc === '#') return '♯';
        if (acc === 'b') return '♭';
        if (acc === 'n') return '♮';
        return '';
    }

    function getUnitDurationInBeats(unit) {
        if (unit.type === 'note') {
            if (unit.duration === '-') return 0.5;
            if (unit.duration === '=') return 0.25;
            return 1.0;
        } else if (unit.type === 'slur') {
            let total = 0;
            if (unit.note1.duration === '-') total += 0.5;
            else if (unit.note1.duration === '=') total += 0.25;
            else total += 1.0;
            if (unit.note2.duration === '-') total += 0.5;
            else if (unit.note2.duration === '=') total += 0.25;
            else total += 1.0;
            return total;
        } else if (unit.type === 'augment') {
            return 1.0;
        }
        return 0;
    }

    function drawDurationLines(x, y, width, duration) {
        if (width <= 0) return;
        ctx.fillStyle = "#000000";
        if (duration === '-') ctx.fillRect(x, y, width, 1.5);
        else if (duration === '=') { ctx.fillRect(x, y, width, 1.5); ctx.fillRect(x, y + 5, width, 1.5); }
    }

    function drawAugmentLineAtNoteLevel(x, y, width, isOverflow) {
        if (width <= 4) return;
        ctx.fillStyle = isOverflow ? "#dc2626" : "#000000";
        ctx.fillRect(x, y - 8, width, 2.5);
    }

    function drawRepeatBarline(x, y, type) {
        ctx.save();
        const topY = y - 22, bottomY = y + 22;
        if (type === 'start') {
            ctx.beginPath(); ctx.moveTo(x, topY); ctx.lineTo(x, bottomY); ctx.lineWidth = 3; ctx.stroke();
            ctx.beginPath(); ctx.moveTo(x + 5, topY); ctx.lineTo(x + 5, bottomY); ctx.lineWidth = 1; ctx.stroke();
            ctx.fillText(":", x + 8, y);
        } else if (type === 'end') {
            ctx.fillText(":", x - 7, y);
            ctx.beginPath(); ctx.moveTo(x, topY); ctx.lineTo(x, bottomY); ctx.lineWidth = 1; ctx.stroke();
            ctx.beginPath(); ctx.moveTo(x + 5, topY); ctx.lineTo(x + 5, bottomY); ctx.lineWidth = 3; ctx.stroke();
        }
        ctx.restore();
    }

    // ========== 核心布局修改 ==========
    function renderScore() {
        const rawText = textarea.value;
        getGlobalSettings();

        if (!rawText.trim()) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.font = "16px sans-serif"; ctx.fillStyle = "#999";
            ctx.fillText("请输入简谱内容...", 50, 80);
            return;
        }

        const linesArray = rawText.split(/\r?\n/);
        let contentLines = [];
        let extractedTitle = '';
        for (let line of linesArray) {
            const title = parseTitleLine(line);
            if (title) { extractedTitle = title; globalTitle = extractedTitle; }
            else contentLines.push(line);
        }

        let metadata = null;
        if (contentLines.length > 0 && contentLines[0].trim().match(/[0-9C-G]/)) {
            metadata = parseMetadataLine(contentLines[0]);
            if (metadata && (metadata.key || metadata.timeSig || metadata.tempo)) {
                updateUIFromMetadata(metadata);
                contentLines = contentLines.slice(1);
            }
        }

        const { beatsPerBar } = getTimeSignature();
        const rowsMeasures = [];
        for (let line of contentLines) {
            if (line.trim() === "") { rowsMeasures.push(null); continue; }
            const measures = parseLineToMeasures(line);
            const rowMeasures = [];
            for (let m of measures) {
                const units = parseMeasureToUnits(m.tokens);
                rowMeasures.push({ units, repeatStart: m.repeatStart, repeatEnd: m.repeatEnd });
            }
            rowsMeasures.push(rowMeasures);
        }

        const crossSlurList = postProcessSlurs(rowsMeasures);

        const startX = 45, rowHeight = 120, noteBaseY = 75, lineYBase = noteBaseY + 18;
        const noteUnitWidth = 48, slurUnitWidth = 62, augmentUnitWidth = 40;

        // ===== 布局计算（均匀间距） =====
        const rowData = [];
        for (let rowIdx = 0; rowIdx < rowsMeasures.length; rowIdx++) {
            const measures = rowsMeasures[rowIdx];
            if (measures === null) { rowData.push(null); continue; }
            let currentX = startX;
            const measurePositions = [];
            for (let mIdx = 0; mIdx < measures.length; mIdx++) {
                const measure = measures[mIdx];
                const measureStartX = currentX;
                const measureUnits = [];
                for (let u of measure.units) {
                    let width = noteUnitWidth;
                    if (u.type === 'slur') width = slurUnitWidth;
                    if (u.type === 'augment') width = augmentUnitWidth;
                    

                    const leftEdge = currentX;
                    const rightEdge = leftEdge + width;
                    const center = leftEdge + width / 2;
                    measureUnits.push({
                        unit: u,
                        x: leftEdge,
                        width: width,
                        center: center,
                        leftEdge: leftEdge,
                        rightEdge: rightEdge
                    });
                    currentX = rightEdge + NOTE_SPACING; // 音符后加间距
                }
                // 最后一个音符后多加了 NOTE_SPACING，需要去掉，以便正确放置右小节线
                const measureEndX = currentX - NOTE_SPACING;
                // 记录左/右小节线位置
                const leftBarX = measureStartX - NOTE_SPACING;
                const rightBarX = measureEndX + NOTE_SPACING;
                measurePositions.push({
                    units: measureUnits,
                    startX: measureStartX,
                    endX: measureEndX,
                    leftBarX: leftBarX,
                    rightBarX: rightBarX,
                    overflowUnits: null,
                    repeatStart: measure.repeatStart,
                    repeatEnd: measure.repeatEnd
                });
                // 下一小节的开始位置 = 右小节线位置 + 小节线宽度 + 间距
                currentX = rightBarX + BARLINE_WIDTH + NOTE_SPACING;
            }
            rowData.push({ measures: measurePositions, totalWidth: currentX });
        }

        // 溢出检测
        for (let rowIdx = 0; rowIdx < rowsMeasures.length; rowIdx++) {
            const measuresUnits = rowsMeasures[rowIdx];
            if (!measuresUnits) continue;
            const rd = rowData[rowIdx];
            for (let mIdx = 0; mIdx < measuresUnits.length; mIdx++) {
                const units = measuresUnits[mIdx].units;
                const overflowSet = new Set();
                let acc = 0;
                for (let uIdx = 0; uIdx < units.length; uIdx++) {
                    acc += getUnitDurationInBeats(units[uIdx]);
                    if (acc > beatsPerBar + 0.001) overflowSet.add(uIdx);
                }
                if (rd && rd.measures[mIdx]) rd.measures[mIdx].overflowUnits = overflowSet;
            }
        }

        // 画布大小
        let maxWidth = startX + 100;
        for (let rd of rowData) if (rd) maxWidth = Math.max(maxWidth, rd.totalWidth);
        let topOffset = globalTitle ? 55 : 30;
        let totalHeight = (rowsMeasures.filter(r => r !== null).length) * rowHeight + topOffset + 40;
        canvas.width = Math.max(maxWidth, 1000);
        canvas.height = Math.max(totalHeight, 300);
        ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // 标题 & 版权
        if (globalTitle) {
            ctx.font = "bold 22px 'Segoe UI', 'Microsoft YaHei', '楷体', serif";
            ctx.fillStyle = "#1e2a3a"; ctx.textAlign = "center";
            ctx.fillText(globalTitle, canvas.width / 2, 32);
            ctx.textAlign = "left";
        }
        if (globalCopyright) {
            ctx.font = "12px 'Segoe UI', 'Microsoft YaHei', '楷体', serif";
            ctx.fillStyle = "#1e2a3a"; ctx.textAlign = "center";
            ctx.fillText(globalCopyright, canvas.width / 2, 52);
            ctx.textAlign = "left";
        }
        ctx.font = "bold 13px monospace"; ctx.fillStyle = "#4a5568"; ctx.textAlign = "left";
        let infoY = globalTitle ? 62 : 32;
        ctx.fillText(`${globalKeySignature}  ${globalBeatsPerBar}/${globalBeatUnit}  ♪=${globalTempo}`, 12, infoY);

        // 位置映射（跨小节连音使用）
        const unitPosMap = {};
        for (let rowIdx = 0; rowIdx < rowData.length; rowIdx++) {
            const rd = rowData[rowIdx];
            if (!rd) continue;
            for (let mIdx = 0; mIdx < rd.measures.length; mIdx++) {
                const m = rd.measures[mIdx];
                for (let uIdx = 0; uIdx < m.units.length; uIdx++) {
                    const item = m.units[uIdx];
                    unitPosMap[`${rowIdx},${mIdx},${uIdx}`] = { x: item.x, center: item.center, leftEdge: item.leftEdge, rightEdge: item.rightEdge };
                }
            }
        }
        const crossSlurDrawList = [];
        for (let cs of crossSlurList) {
            const srcKey = `${cs.sourcePos.rowIdx},${cs.sourcePos.measureIdx},${cs.sourcePos.unitIdx}`;
            const tgtKey = `${cs.targetPos.rowIdx},${cs.targetPos.measureIdx},${cs.targetPos.unitIdx}`;
            const srcPos = unitPosMap[srcKey], tgtPos = unitPosMap[tgtKey];
            if (srcPos && tgtPos) {
                const srcMeasure = rowData[cs.sourcePos.rowIdx]?.measures[cs.sourcePos.measureIdx];
                const barX = srcMeasure ? srcMeasure.rightBarX : (srcPos.rightEdge + tgtPos.leftEdge) / 2;
                crossSlurDrawList.push({
                    sourceX: srcPos.center + 8,
                    targetX: tgtPos.center - 8,
                    barlineX: barX,
                    letter: cs.letter,
                    sourceRowIdx: cs.sourcePos.rowIdx,
                    targetRowIdx: cs.targetPos.rowIdx
                });
            }
        }

        // 绘制每一行
        for (let rowIdx = 0; rowIdx < rowsMeasures.length; rowIdx++) {
            const rd = rowData[rowIdx];
            if (!rd) continue;
            let rowOffset = globalTitle ? 55 : 20;
            const noteY = noteBaseY + rowIdx * rowHeight + rowOffset;
            const lineY = lineYBase + rowIdx * rowHeight + rowOffset;

            // 画音符
            for (let mIdx = 0; mIdx < rd.measures.length; mIdx++) {
                const m = rd.measures[mIdx];
                const overflowSet = m.overflowUnits || new Set();

                for (let uIdx = 0; uIdx < m.units.length; uIdx++) {
                    const item = m.units[uIdx];
                    const u = item.unit;
                    const isOverflow = overflowSet.has(uIdx);
                    const textColor = isOverflow ? "#dc2626" : "#000000";
                    const dotColor = isOverflow ? "#dc2626" : "#b91c1c";

                    if (u.type === 'slur') {
                        const leftX = item.x, rightX = item.x + item.width + 10;
                        const cx1 = leftX + item.width/3.2, cx2 = leftX + item.width - item.width/3.2;
                        const d1 = getNoteDisplay(u.note1.pitch), d2 = getNoteDisplay(u.note2.pitch);
                        const acc1 = getAccidentalSymbol(u.note1.accidental), acc2 = getAccidentalSymbol(u.note2.accidental);

                        ctx.font = "bold 26px 'Times New Roman', serif"; ctx.fillStyle = textColor;
                        if (acc1) { ctx.font = "bold 20px serif"; ctx.fillStyle = "#b45309"; ctx.fillText(acc1, cx1 - 10, noteY-10); ctx.font = "bold 26px 'Times New Roman', serif"; ctx.fillStyle = textColor; }
                        ctx.fillText(d1.digit, cx1, noteY);
                        if (acc2) { ctx.font = "bold 20px serif"; ctx.fillStyle = "#b45309"; ctx.fillText(acc2, cx2 - 5, noteY-10); ctx.font = "bold 26px 'Times New Roman', serif"; ctx.fillStyle = textColor; }
                        ctx.fillText(d2.digit, cx2+5, noteY);

                        ctx.font = "bold 20px serif"; ctx.fillStyle = dotColor;
                        for (let i = 0; i < d1.upDots; i++) ctx.fillText("·", cx1 - 2, noteY - 24 - i*12);
                        for (let i = 0; i < d2.upDots; i++) ctx.fillText("·", cx2 + 3, noteY - 24 - i*12);
                        ctx.fillStyle = textColor;
                        for (let i = 0; i < d1.downDots; i++) ctx.fillText("·", cx1 - 2, noteY + 24 + i*12);
                        for (let i = 0; i < d2.downDots; i++) ctx.fillText("·", cx2 + 3, noteY + 24 + i*12);

                        const slurLineY = noteY + 18;
                        if (u.note1.duration === '-' || u.note1.duration === '=') drawDurationLines(cx1 - 5, slurLineY, 20, u.note1.duration);
                        if (u.note2.duration === '-' || u.note2.duration === '=') drawDurationLines(cx2 + 2, slurLineY, 20, u.note2.duration);

                        ctx.save();                              // ★ 添加保存
                        const arcTopY = noteY - 38;
                        ctx.beginPath();
                        ctx.moveTo(leftX+11, arcTopY);
                        ctx.quadraticCurveTo((leftX+rightX)/2, arcTopY-16, rightX-6, arcTopY);
                        ctx.strokeStyle = isOverflow ? "#dc2626" : "#2c5282";
                        ctx.lineWidth = 1.5;
                        ctx.stroke();
                        ctx.font = "bold 12px monospace";
                        ctx.fillStyle = isOverflow ? "#dc2626" : "#b45309";
                        ctx.fillText(u.letter, (leftX+rightX)/2, arcTopY-10);
                        ctx.restore();                           // ★ 添加恢复
                    } else if (u.type === 'note') {
                        const d = getNoteDisplay(u.pitch);
                        const acc = getAccidentalSymbol(u.accidental);
                        ctx.font = "bold 26px 'Times New Roman', serif"; ctx.fillStyle = textColor;
                        if (acc) { ctx.font = "bold 20px serif"; ctx.fillStyle = "#b45309"; ctx.fillText(acc, item.center - 15, noteY-10); ctx.font = "bold 26px 'Times New Roman', serif"; ctx.fillStyle = textColor; }
                        ctx.fillText(d.digit, item.center, noteY);

                        ctx.font = "bold 20px serif"; ctx.fillStyle = dotColor;
                        for (let i = 0; i < d.upDots; i++) ctx.fillText("·", item.center - 4, noteY - 24 - i*12);
                        ctx.fillStyle = textColor;
                        for (let i = 0; i < d.downDots; i++) ctx.fillText("·", item.center - 4, noteY + 24 + i*12);
                        if (u.techSuffix) {
                            ctx.font = "bold 11px monospace"; ctx.fillStyle = isOverflow ? "#dc2626" : "#b45309";
                            ctx.fillText(u.techSuffix, item.center+18, noteY-18);
                        }
                    } else if (u.type === 'augment') {
                        drawAugmentLineAtNoteLevel(item.leftEdge + 6, noteY, item.width - 12, isOverflow);
                    }
                }
            }

            // 在 renderScore() 中绘制小节线处改为：
            // 画小节线
            // 1. 行首左小节线（第一个小节的左边界）
            const firstMeasure = rd.measures[0];
            ctx.beginPath();
            ctx.strokeStyle = "#000000";
            ctx.lineWidth = NORMAL_BARLINE_WIDTH;
            if (firstMeasure.repeatStart) {
                drawRepeatBarline(firstMeasure.leftBarX, noteY, 'start');
            } else {
                ctx.moveTo(firstMeasure.leftBarX, noteY - 22);
                ctx.lineTo(firstMeasure.leftBarX, noteY + 22);
                ctx.stroke();
            }

            // 2. 依次绘制每个小节的右小节线（中间部分不再画左线）
            for (let mIdx = 0; mIdx < rd.measures.length; mIdx++) {
                const m = rd.measures[mIdx];
                const x = m.rightBarX;

                ctx.beginPath();
                ctx.strokeStyle = "#000000";
                ctx.lineWidth = NORMAL_BARLINE_WIDTH;

                if (m.repeatEnd) {
                    drawRepeatBarline(x+8, noteY, 'end');
                } else {
                    ctx.moveTo(x+8, noteY - 22);
                    ctx.lineTo(x+8, noteY + 22);
                    ctx.stroke();
                }
            }
            // 时值线
            // 时值线 - 按相同减时线合并（连续八分/十六分音符连成一条线）
            for (let m of rd.measures) {
                const groups = [];
                let currentGroup = null;
                // 分组：连续同 duration 的音符归为一组
                for (let item of m.units) {
                    if (item.unit.type === 'note' && (item.unit.duration === '-' || item.unit.duration === '=')) {
                        const dur = item.unit.duration;
                        if (currentGroup && currentGroup.duration === dur) {
                            currentGroup.items.push(item);
                        } else {
                            if (currentGroup) groups.push(currentGroup);
                            currentGroup = { duration: dur, items: [item] };
                        }
                    } else {
                        if (currentGroup) {
                            groups.push(currentGroup);
                            currentGroup = null;
                        }
                    }
                }
                if (currentGroup) groups.push(currentGroup);

                // 绘制每一组
                for (let group of groups) {
                    if (group.items.length === 0) continue;
                    const first = group.items[0];
                    const last = group.items[group.items.length - 1];
                    // 线的起点和终点（偏移量与原版保持一致）
                    const startX = first.leftEdge + 13;
                    const endX = last.leftEdge + last.width - 6;
                    const lineWidth = endX - startX;
                    if (lineWidth > 0) {
                        drawDurationLines(startX, lineY, lineWidth, group.duration);
                    } else {
                        // 极端情况（例如音符宽度异常）降级为逐个绘制
                        for (let item of group.items) {
                            drawDurationLines(item.leftEdge + 13, lineY, item.width - 6, group.duration);
                        }
                    }
                }
            }
        }

        // 跨小节连音线
        for (let cs of crossSlurDrawList) {
            const rowOffset = globalTitle ? 55 : 20;
            const srcY = noteBaseY + cs.sourceRowIdx * rowHeight + rowOffset;
            const tgtY = noteBaseY + cs.targetRowIdx * rowHeight + rowOffset;
            const arcTopY = (srcY + tgtY) / 2 - 42;
            const cpY = arcTopY - 18;
            ctx.save();
            ctx.beginPath();
            ctx.moveTo(cs.sourceX, arcTopY);
            ctx.quadraticCurveTo(cs.barlineX, cpY, cs.targetX, arcTopY);
            ctx.strokeStyle = "#2c5282"; ctx.lineWidth = 1.8; ctx.stroke();
            ctx.font = "bold 12px monospace"; ctx.fillStyle = "#b45309"; ctx.textAlign = "center";
            ctx.fillText(cs.letter, cs.barlineX, cpY - 4);
            ctx.textAlign = "left";
            ctx.restore();
        }

        currentRowsMeasures = rowsMeasures;
        if (canvas.height > container.clientHeight) container.scrollTop = container.scrollHeight;
    }

    // 导出接口
    window.getScoreData = function() {
        return {
            rowsMeasures: currentRowsMeasures,
            beatsPerBar: globalBeatsPerBar,
            beatUnit: globalBeatUnit,
            tempo: globalTempo,
            keySig: globalKeySignature,
            title: globalTitle
        };
    };

    const examples = {
        basic: `title: 基础音阶\n1=C 4/4 120\n| 1 2 3 4 | 5 6 7 1' | 1' 7 6 5 | 4 3 2 1 |`,
        accidental: `title: 变音记号演示\n1=C 4/4 100\n| #1 b3 n5 6 | #4 b7 2 #5 | b3 #5 7 n3 | #1 #2 #3 #4 |`,
        repeat: `title: 反复记号练习\n1=G 3/4 80\n|: 1 2 3 | 4 5 6 :| 7 1' 2' | 3' 2' 1' |\n|: 5 6 7 | 1' 2' 3' :| 4' 3' 2' | 1' - - |`,
        slur: `title: 跨小节连音演示\n1=C 4/4 100\n| 3 ^ | 4 5 6 | 7 1' ^ | 2' - - |\n| 5 | ^ 6 7 1' | 2' 3' ^ | 4' 3' 2' |\n| 1 2 3 4 | 5 ^ 6 7 | 1' - - - |`,
        full: `title: 完整歌曲片段\n1=C 4/4 120\n|: #1 2 b3 4 | 5 6 b7 1' | 1' b7 6 5 | 4 b3 2 #1 :|\n| n5 5 6 5 | 1' 2' 3' 2' | #4 #5 #6 7 | 1'' - - - |`
    };

    function setExample(txt) { textarea.value = txt; renderScore(); }

    textarea.addEventListener('input', renderScore);
    beatNumeratorInput.addEventListener('input', renderScore);
    beatDenominatorInput.addEventListener('input', renderScore);
    keySignatureInput.addEventListener('input', renderScore);
    tempoInput.addEventListener('input', renderScore);
    window.addEventListener('resize', renderScore);

    document.getElementById('basicExampleBtn').addEventListener('click', () => setExample(examples.basic));
    document.getElementById('accidentalBtn').addEventListener('click', () => setExample(examples.accidental));
    document.getElementById('repeatBtn').addEventListener('click', () => setExample(examples.repeat));
    document.getElementById('slurExampleBtn').addEventListener('click', () => setExample(examples.slur));
    document.getElementById('fullSongBtn').addEventListener('click', () => setExample(examples.full));
    document.getElementById('clearBtn').addEventListener('click', () => { textarea.value = ''; renderScore(); });

    renderScore();
})();