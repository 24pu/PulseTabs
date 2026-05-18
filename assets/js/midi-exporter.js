class MidiExporter {
    // 调号转 MIDI 半音偏移
    getKeyOffset(keySig) {
        const map = {
            '1=C':0, '1=G':7, '1=D':2, '1=A':9, '1=E':4, '1=B':11, '1=F#':6, '1=C#':1,
            '1=F':-7, '1=Bb':-2, '1=Eb':-5, '1=Ab':-8, '1=Db':-1, '1=Gb':-6, '1=Cb':-10
        };
        return map[keySig] || 0;
    }

    // 将乐谱数据转换为 MIDI 事件（按绝对 tick 排序）
    convertToMidiEvents(rowsMeasures, beatsPerBar, beatUnit, tempo, keyOffset) {
        const events = [];
        let absTick = 0;
        const ticksPerBeat = 480;

        for (let row of rowsMeasures) {
            if (!row) continue;
            for (let measure of row) {
                for (let unit of measure.units) {
                    if (unit.type === 'note') {
                        const midi = this.getMidiPitch(unit.pitch, unit.accidental, keyOffset);
                        let durTicks = ticksPerBeat;
                        if (unit.duration === '-') durTicks = ticksPerBeat / 2;
                        if (unit.duration === '=') durTicks = ticksPerBeat / 4;
                        events.push({ pitch: midi, startTick: absTick, durationTicks: durTicks });
                        absTick += durTicks;
                    } else if (unit.type === 'slur') {
                        for (let note of [unit.note1, unit.note2]) {
                            const midi = this.getMidiPitch(note.pitch, note.accidental, keyOffset);
                            let durTicks = ticksPerBeat;
                            if (note.duration === '-') durTicks = ticksPerBeat / 2;
                            if (note.duration === '=') durTicks = ticksPerBeat / 4;
                            events.push({ pitch: midi, startTick: absTick, durationTicks: durTicks });
                            absTick += durTicks;
                        }
                    } else if (unit.type === 'augment') {
                        absTick += ticksPerBeat;
                    }
                }
            }
        }
        return { events, totalTicks: absTick };
    }

    // 计算 MIDI 音高（0-127 安全范围）
    getMidiPitch(pitchStr, accidental, keyOffset) {
        const basePitch = parseInt(pitchStr[0]);
        if (isNaN(basePitch) || basePitch < 1 || basePitch > 7) return 60; // 默认中央 C
        const upDots = (pitchStr.match(/'/g) || []).length;
        const downDots = (pitchStr.match(/,/g) || []).length;
        let midi = [0, 0, 2, 4, 5, 7, 9, 11][basePitch] + 60;
        midi += upDots * 12 - downDots * 12;
        midi += keyOffset;
        if (accidental === '#') midi += 1;
        if (accidental === 'b') midi -= 1;
        return Math.max(0, Math.min(127, Math.round(midi))); // 钳制在 0-127
    }

    // 导出 MIDI 文件
    exportMidi(rowsMeasures, beatsPerBar, beatUnit, tempo, keySig, fileName) {
        const keyOffset = this.getKeyOffset(keySig);
        const { events } = this.convertToMidiEvents(rowsMeasures, beatsPerBar, beatUnit, tempo, keyOffset);
        if (events.length === 0) return;

        const ticksPerBeat = 480;
        const tempoMicro = Math.round(60000000 / tempo);

        // ========== 构建标准 MIDI 文件头（14 字节）==========
        const header = [
            0x4D, 0x54, 0x68, 0x64,  // "MThd"
            0x00, 0x00, 0x00, 0x06,  // 长度 6
            0x00, 0x00,                // 格式 0
            0x00, 0x01,                // 轨道数 1
            (ticksPerBeat >> 8) & 0xFF, ticksPerBeat & 0xFF  // 四分音符 tick 数
        ];

        // ========== 构建轨道数据 ==========
        const trackData = [];

        // 1. 速度元事件
        trackData.push(0x00);                        // delta=0
        trackData.push(0xFF, 0x51, 0x03);           // 设置速度
        trackData.push((tempoMicro >> 16) & 0xFF);
        trackData.push((tempoMicro >> 8) & 0xFF);
        trackData.push(tempoMicro & 0xFF);

        // 2. 拍号元事件
        trackData.push(0x00);                        // delta=0
        trackData.push(0xFF, 0x58, 0x04);           // 拍号
        trackData.push(beatsPerBar, 2, 24, 8);      // 标准拍号参数

        // 可选：轨道名（便于软件识别）
        const trackName = '24pu.com';
        trackData.push(0x00);                        // delta=0
        trackData.push(0xFF, 0x03, trackName.length);// 元事件 轨道名
        for (let i = 0; i < trackName.length; i++) {
            trackData.push(trackName.charCodeAt(i));
        }

        // 排序所有音符事件（按开始时间）
        events.sort((a, b) => a.startTick - b.startTick);

        let prevTick = 0; // 上一个事件的绝对 tick
        for (let ev of events) {
            // 计算 delta（相对时间）
            let delta = ev.startTick - prevTick;
            prevTick = ev.startTick;

            // 写入变长 delta 时间
            this.writeVarLen(trackData, delta);

            // 写入 Note On 事件
            trackData.push(0x90, ev.pitch, 80);

            // 写入 Note Off 事件（delta 为音符持续时长）
            this.writeVarLen(trackData, ev.durationTicks);
            trackData.push(0x80, ev.pitch, 0);
        }

        // 轨道结束元事件
        trackData.push(0x00, 0xFF, 0x2F, 0x00);

        // ========== 组装轨道块 ==========
        const trackChunk = [0x4D, 0x54, 0x72, 0x6B]; // "MTrk"
        const trackLen = trackData.length;
        trackChunk.push(
            (trackLen >> 24) & 0xFF,
            (trackLen >> 16) & 0xFF,
            (trackLen >> 8) & 0xFF,
            trackLen & 0xFF
        );

        // 合并所有字节
        const midiArray = [...header, ...trackChunk, ...trackData];

        // 下载文件
        const blob = new Blob([new Uint8Array(midiArray)], { type: 'audio/midi' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        a.click();
        URL.revokeObjectURL(url);
    }

    // 写入变长数值（用于 delta 时间）
    writeVarLen(array, value) {
        if (value < 0) value = 0;
        const buffer = [];
        buffer.push(value & 0x7F);
        value >>= 7;
        while (value > 0) {
            buffer.push((value & 0x7F) | 0x80);
            value >>= 7;
        }
        // 反序写入
        for (let i = buffer.length - 1; i >= 0; i--) {
            array.push(buffer[i]);
        }
    }
}