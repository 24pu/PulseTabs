// assets/js/musicxml-exporter.js

class MusicXMLExporter {
    // 调号映射返回 fifths 值
    getKeyFifths(keySig) {
        const map = {
            '1=C': 0,   '1=G': 1,   '1=D': 2,   '1=A': 3,   '1=E': 4,   '1=B': 5,   '1=F#': 6,   '1=C#': 7,
            '1=F': -1,  '1=Bb': -2, '1=Eb': -3, '1=Ab': -4, '1=Db': -5, '1=Gb': -6, '1=Cb': -7
        };
        return map[keySig] !== undefined ? map[keySig] : 0;
    }

    // MIDI 音高转换为音名、升降号、八度
    midiToStepAlterOctave(midi) {
        // 自然音顺序：C C# D D# E F F# G G# A A# B
        const naturalSteps = ['C', 'C', 'D', 'D', 'E', 'F', 'F', 'G', 'G', 'A', 'A', 'B'];
        const alterValues  = [ 0,   1,   0,   1,   0,   0,   1,   0,   1,   0,   1,   0];
        const idx = midi % 12;
        const octave = Math.floor(midi / 12) - 1;
        return {
            step: naturalSteps[idx],
            alter: alterValues[idx],
            octave: octave
        };
    }

    // 简谱音符 -> MIDI 音高
    getMidiFromJianpu(pitchStr, accidental, keyOffset) {
        const baseDigit = parseInt(pitchStr[0]);
        // 1=C4 (60) 映射
        const baseMidi = [0, 0, 2, 4, 5, 7, 9, 11][baseDigit] + 60;
        const upDots = (pitchStr.match(/'/g) || []).length;
        const downDots = (pitchStr.match(/,/g) || []).length;
        let midi = baseMidi + upDots * 12 - downDots * 12;
        midi += keyOffset;
        if (accidental === '#') midi += 1;
        if (accidental === 'b') midi -= 1;
        return midi;
    }

    // keyOffset 计算（与 MIDI 导出一致）
    getKeyOffset(keySig) {
        const map = {
            '1=C':0, '1=G':7, '1=D':2, '1=A':9, '1=E':4, '1=B':11, '1=F#':6, '1=C#':1,
            '1=F':-7, '1=Bb':-2, '1=Eb':-5, '1=Ab':-8, '1=Db':-1, '1=Gb':-6, '1=Cb':-10
        };
        return map[keySig] || 0;
    }

    exportToFile(rowsMeasures, beatsPerBar, beatUnit, tempo, keySig, title) {
        if (!rowsMeasures || rowsMeasures.length === 0) {
            alert('没有乐谱数据');
            return;
        }

        const divisions = 480;
        const keyOffset = this.getKeyOffset(keySig);
        const fifths = this.getKeyFifths(keySig);

        let xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
        xml += '<!DOCTYPE score-partwise PUBLIC "-//Recordare//DTD MusicXML 3.1 Partwise//EN" "http://www.musicxml.org/dtds/partwise.dtd">\n';
        xml += '<score-partwise version="3.1">\n';

         // 作品信息（标题）
        xml += `  <work><work-title>${title || '简谱'}</work-title></work>\n`;
        xml += `  <movement-title>${title || '简谱'}</movement-title>\n`;

        // credit：标题与副标题 24pu.com
        xml += '  <credit page="1">\n';
        xml += `    <credit-words valign="top" font-size="24" default-x="655" justify="center" default-y="1810">${title || '简谱'}</credit-words>\n`;
        xml += '  </credit>\n';
        xml += '  <credit page="1">\n';
        xml += '    <credit-words valign="top" font-size="14" default-x="655" justify="center" default-y="1760">24pu.com</credit-words>\n';
        xml += '  </credit>\n';

        xml += '  <part-list>\n';
        xml += '    <score-part id="P1">\n';
        xml += '      <part-name>Jianpu</part-name>\n';
        xml += '      <part-abbreviation>JP</part-abbreviation>\n';
        xml += '    </score-part>\n';
        xml += '  </part-list>\n';
        xml += '  <part id="P1">\n';
        
        let measureNumber = 0;
        let slurNumber = 1;
        const openSlurs = []; // 用于跟踪未闭合的连音线

        for (let row of rowsMeasures) {
            if (!row) continue;
            for (let measure of row) {
                measureNumber++;
                xml += `    <measure number="${measureNumber}">\n`;

                // 第一个小节的 attributes 和 direction（速度）
                if (measureNumber === 1) {
                    xml += '      <attributes>\n';
                    xml += `        <divisions>${divisions}</divisions>\n`;
                    xml += `        <key><fifths>${fifths}</fifths><mode>major</mode></key>\n`;
                    xml += `        <time><beats>${beatsPerBar}</beats><beat-type>${beatUnit}</beat-type></time>\n`;
                    xml += `        <clef><sign>G</sign><line>2</line></clef>\n`;
                    xml += '      </attributes>\n';

                    // 速度标记：使用 direction 和 sound tempo
                    xml += '      <direction placement="above">\n';
                    xml += '        <direction-type>\n';
                    xml += `          <metronome>\n`;
                    xml += `            <beat-unit>quarter</beat-unit>\n`;
                    xml += `            <per-minute>${tempo}</per-minute>\n`;
                    xml += `          </metronome>\n`;
                    xml += '        </direction-type>\n';
                    xml += `        <sound tempo="${tempo}"/>\n`;
                    xml += '      </direction>\n';
                }

                // 处理反复开始（左）
                if (measure.repeatStart) {
                    xml += '      <barline location="left">\n';
                    xml += '        <bar-style>heavy-light</bar-style>\n';
                    xml += '        <repeat direction="forward"/>\n';
                    xml += '      </barline>\n';
                }

                let prevNoteDurationAcc = 0; // 增时线累积时值

                for (let unit of measure.units) {
                    if (unit.type === 'note') {
                        const midi = this.getMidiFromJianpu(unit.pitch, unit.accidental, keyOffset);
                        const noteInfo = this.midiToStepAlterOctave(midi);

                        let duration = divisions;
                        let typeName = 'quarter';
                        if (unit.duration === '-') { duration = divisions / 2; typeName = 'eighth'; }
                        else if (unit.duration === '=') { duration = divisions / 4; typeName = '16th'; }

                        // 加上增时线积累
                        duration += prevNoteDurationAcc;
                        prevNoteDurationAcc = 0;

                        xml += this.buildNoteXML(noteInfo, duration, typeName, unit);
                    }
                    else if (unit.type === 'slur') {
                        // 连音符里有两个音符
                        for (let ni = 0; ni < 2; ni++) {
                            const noteData = ni === 0 ? unit.note1 : unit.note2;
                            const midi = this.getMidiFromJianpu(noteData.pitch, noteData.accidental, keyOffset);
                            const noteInfo = this.midiToStepAlterOctave(midi);

                            let duration = divisions;
                            let typeName = 'quarter';
                            if (noteData.duration === '-') { duration = divisions / 2; typeName = 'eighth'; }
                            else if (noteData.duration === '=') { duration = divisions / 4; typeName = '16th'; }

                            duration += prevNoteDurationAcc;
                            prevNoteDurationAcc = 0;

                            // 连音标记：第一个音符 start，第二个 stop
                            let slurXml = '';
                            if (ni === 0) {
                                slurXml = `        <notations><slur type="start" number="${slurNumber}"/></notations>\n`;
                            } else {
                                slurXml = `        <notations><slur type="stop" number="${slurNumber}"/></notations>\n`;
                            }
                            const noteXml = this.buildNoteXMLWithSlur(noteInfo, duration, typeName, slurXml);
                            xml += noteXml;
                        }
                        slurNumber++;
                    }
                    else if (unit.type === 'augment') {
                        // 增时线，累加一拍
                        prevNoteDurationAcc += divisions;
                    }
                }

                // 处理反复结束（右）
                if (measure.repeatEnd) {
                    xml += '      <barline location="right">\n';
                    xml += '        <bar-style>light-heavy</bar-style>\n';
                    xml += '        <repeat direction="backward"/>\n';
                    xml += '      </barline>\n';
                }

                xml += '    </measure>\n';
            }
        }

        xml += '  </part>\n';
        xml += '</score-partwise>';

        const blob = new Blob([xml], { type: 'application/vnd.recordare.musicxml+xml' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = (title || 'jianpu') + '.musicxml';
        a.click();
        URL.revokeObjectURL(url);
    }

    // 普通音符 XML（无连音）
    buildNoteXML(noteInfo, duration, type, unit) {
        let xml = '      <note>\n';
        xml += '        <pitch>\n';
        xml += `          <step>${noteInfo.step}</step>\n`;
        if (noteInfo.alter !== 0) xml += `          <alter>${noteInfo.alter}</alter>\n`;
        xml += `          <octave>${noteInfo.octave}</octave>\n`;
        xml += '        </pitch>\n';
        xml += `        <duration>${duration}</duration>\n`;
        xml += `        <type>${type}</type>\n`;
        // 跨小节连音（如果有 crossSlurTo/From）
        if (unit.crossSlurTo) {
            xml += '        <notations><slur type="start" number="1"/></notations>\n';
        } else if (unit.crossSlurFrom) {
            xml += '        <notations><slur type="stop" number="1"/></notations>\n';
        }
        xml += '      </note>\n';
        return xml;
    }

    // 带连音标记的音符
    buildNoteXMLWithSlur(noteInfo, duration, type, slurXml) {
        let xml = '      <note>\n';
        xml += '        <pitch>\n';
        xml += `          <step>${noteInfo.step}</step>\n`;
        if (noteInfo.alter !== 0) xml += `          <alter>${noteInfo.alter}</alter>\n`;
        xml += `          <octave>${noteInfo.octave}</octave>\n`;
        xml += '        </pitch>\n';
        xml += `        <duration>${duration}</duration>\n`;
        xml += `        <type>${type}</type>\n`;
        xml += slurXml;
        xml += '      </note>\n';
        return xml;
    }
}