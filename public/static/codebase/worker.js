(function () {
  'use strict';

  /*
   * Copyright 2017 Sam Thorogood. All rights reserved.
   *
   * Licensed under the Apache License, Version 2.0 (the "License"); you may not
   * use this file except in compliance with the License. You may obtain a copy of
   * the License at
   *
   *     http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
   * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
   * License for the specific language governing permissions and limitations under
   * the License.
   */

  /**
   * @fileoverview Polyfill for TextEncoder and TextDecoder.
   *
   * You probably want `text.min.js`, and not this file directly.
   */

  (function(scope) {

  // fail early
  if (scope['TextEncoder'] && scope['TextDecoder']) {
    return false;
  }

  /**
   * @constructor
   * @param {string=} utfLabel
   */
  function FastTextEncoder(utfLabel='utf-8') {
    if (utfLabel !== 'utf-8') {
      throw new RangeError(
        `Failed to construct 'TextEncoder': The encoding label provided ('${utfLabel}') is invalid.`);
    }
  }

  Object.defineProperty(FastTextEncoder.prototype, 'encoding', {value: 'utf-8'});

  /**
   * @param {string} string
   * @param {{stream: boolean}=} options
   * @return {!Uint8Array}
   */
  FastTextEncoder.prototype.encode = function(string, options={stream: false}) {
    if (options.stream) {
      throw new Error(`Failed to encode: the 'stream' option is unsupported.`);
    }

    let pos = 0;
    const len = string.length;

    let at = 0;  // output position
    let tlen = Math.max(32, len + (len >> 1) + 7);  // 1.5x size
    let target = new Uint8Array((tlen >> 3) << 3);  // ... but at 8 byte offset

    while (pos < len) {
      let value = string.charCodeAt(pos++);
      if (value >= 0xd800 && value <= 0xdbff) {
        // high surrogate
        if (pos < len) {
          const extra = string.charCodeAt(pos);
          if ((extra & 0xfc00) === 0xdc00) {
            ++pos;
            value = ((value & 0x3ff) << 10) + (extra & 0x3ff) + 0x10000;
          }
        }
        if (value >= 0xd800 && value <= 0xdbff) {
          continue;  // drop lone surrogate
        }
      }

      // expand the buffer if we couldn't write 4 bytes
      if (at + 4 > target.length) {
        tlen += 8;  // minimum extra
        tlen *= (1.0 + (pos / string.length) * 2);  // take 2x the remaining
        tlen = (tlen >> 3) << 3;  // 8 byte offset

        const update = new Uint8Array(tlen);
        update.set(target);
        target = update;
      }

      if ((value & 0xffffff80) === 0) {  // 1-byte
        target[at++] = value;  // ASCII
        continue;
      } else if ((value & 0xfffff800) === 0) {  // 2-byte
        target[at++] = ((value >>  6) & 0x1f) | 0xc0;
      } else if ((value & 0xffff0000) === 0) {  // 3-byte
        target[at++] = ((value >> 12) & 0x0f) | 0xe0;
        target[at++] = ((value >>  6) & 0x3f) | 0x80;
      } else if ((value & 0xffe00000) === 0) {  // 4-byte
        target[at++] = ((value >> 18) & 0x07) | 0xf0;
        target[at++] = ((value >> 12) & 0x3f) | 0x80;
        target[at++] = ((value >>  6) & 0x3f) | 0x80;
      } else {
        // FIXME: do we care
        continue;
      }

      target[at++] = (value & 0x3f) | 0x80;
    }

    return target.slice(0, at);
  };

  /**
   * @constructor
   * @param {string=} utfLabel
   * @param {{fatal: boolean}=} options
   */
  function FastTextDecoder(utfLabel='utf-8', options={fatal: false}) {
    if (utfLabel !== 'utf-8') {
      throw new RangeError(
        `Failed to construct 'TextDecoder': The encoding label provided ('${utfLabel}') is invalid.`);
    }
    if (options.fatal) {
      throw new Error(`Failed to construct 'TextDecoder': the 'fatal' option is unsupported.`);
    }
  }

  Object.defineProperty(FastTextDecoder.prototype, 'encoding', {value: 'utf-8'});

  Object.defineProperty(FastTextDecoder.prototype, 'fatal', {value: false});

  Object.defineProperty(FastTextDecoder.prototype, 'ignoreBOM', {value: false});

  /**
   * @param {(!ArrayBuffer|!ArrayBufferView)} buffer
   * @param {{stream: boolean}=} options
   */
  FastTextDecoder.prototype.decode = function(buffer, options={stream: false}) {
    if (options['stream']) {
      throw new Error(`Failed to decode: the 'stream' option is unsupported.`);
    }

    const bytes = new Uint8Array(buffer);
    let pos = 0;
    const len = bytes.length;
    const out = [];

    while (pos < len) {
      const byte1 = bytes[pos++];
      if (byte1 === 0) {
        break;  // NULL
      }
    
      if ((byte1 & 0x80) === 0) {  // 1-byte
        out.push(byte1);
      } else if ((byte1 & 0xe0) === 0xc0) {  // 2-byte
        const byte2 = bytes[pos++] & 0x3f;
        out.push(((byte1 & 0x1f) << 6) | byte2);
      } else if ((byte1 & 0xf0) === 0xe0) {
        const byte2 = bytes[pos++] & 0x3f;
        const byte3 = bytes[pos++] & 0x3f;
        out.push(((byte1 & 0x1f) << 12) | (byte2 << 6) | byte3);
      } else if ((byte1 & 0xf8) === 0xf0) {
        const byte2 = bytes[pos++] & 0x3f;
        const byte3 = bytes[pos++] & 0x3f;
        const byte4 = bytes[pos++] & 0x3f;

        // this can be > 0xffff, so possibly generate surrogates
        let codepoint = ((byte1 & 0x07) << 0x12) | (byte2 << 0x0c) | (byte3 << 0x06) | byte4;
        if (codepoint > 0xffff) {
          // codepoint &= ~0x10000;
          codepoint -= 0x10000;
          out.push((codepoint >>> 10) & 0x3ff | 0xd800);
          codepoint = 0xdc00 | codepoint & 0x3ff;
        }
        out.push(codepoint);
      }
    }

    return String.fromCharCode.apply(null, out);
  };

  scope['TextEncoder'] = FastTextEncoder;
  scope['TextDecoder'] = FastTextDecoder;

  }(typeof window !== 'undefined' ? window : (typeof global !== 'undefined' ? global : self)));

  (function() {
      const __exports = {};
      let wasm;

      let cachegetUint8Memory = null;
      function getUint8Memory() {
          if (cachegetUint8Memory === null || cachegetUint8Memory.buffer !== wasm.memory.buffer) {
              cachegetUint8Memory = new Uint8Array(wasm.memory.buffer);
          }
          return cachegetUint8Memory;
      }

      let WASM_VECTOR_LEN = 0;

      function passArray8ToWasm(arg) {
          const ptr = wasm.__wbindgen_malloc(arg.length * 1);
          getUint8Memory().set(arg, ptr / 1);
          WASM_VECTOR_LEN = arg.length;
          return ptr;
      }

      const heap = new Array(32);

      heap.fill(undefined);

      heap.push(undefined, null, true, false);

  function getObject(idx) { return heap[idx]; }

  let heap_next = heap.length;

  function dropObject(idx) {
      if (idx < 36) return;
      heap[idx] = heap_next;
      heap_next = idx;
  }

  function takeObject(idx) {
      const ret = getObject(idx);
      dropObject(idx);
      return ret;
  }

  let cachegetUint32Memory = null;
  function getUint32Memory() {
      if (cachegetUint32Memory === null || cachegetUint32Memory.buffer !== wasm.memory.buffer) {
          cachegetUint32Memory = new Uint32Array(wasm.memory.buffer);
      }
      return cachegetUint32Memory;
  }

  function getArrayJsValueFromWasm(ptr, len) {
      const mem = getUint32Memory();
      const slice = mem.subarray(ptr / 4, ptr / 4 + len);
      const result = [];
      for (let i = 0; i < slice.length; i++) {
          result.push(takeObject(slice[i]));
      }
      return result;
  }

  let cachedGlobalArgumentPtr = null;
  function globalArgumentPtr() {
      if (cachedGlobalArgumentPtr === null) {
          cachedGlobalArgumentPtr = wasm.__wbindgen_global_argument_ptr();
      }
      return cachedGlobalArgumentPtr;
  }

  let cachedTextEncoder = new TextEncoder('utf-8');

  let passStringToWasm;
  if (typeof cachedTextEncoder.encodeInto === 'function') {
      passStringToWasm = function(arg) {

          let size = arg.length;
          let ptr = wasm.__wbindgen_malloc(size);
          let writeOffset = 0;
          while (true) {
              const view = getUint8Memory().subarray(ptr + writeOffset, ptr + size);
              const { read, written } = cachedTextEncoder.encodeInto(arg, view);
              arg = arg.substring(read);
              writeOffset += written;
              if (arg.length === 0) {
                  break;
              }
              ptr = wasm.__wbindgen_realloc(ptr, size, size * 2);
              size *= 2;
          }
          WASM_VECTOR_LEN = writeOffset;
          return ptr;
      };
  } else {
      passStringToWasm = function(arg) {

          const buf = cachedTextEncoder.encode(arg);
          const ptr = wasm.__wbindgen_malloc(buf.length);
          getUint8Memory().set(buf, ptr);
          WASM_VECTOR_LEN = buf.length;
          return ptr;
      };
  }

  let cachedTextDecoder = new TextDecoder('utf-8');

  function getStringFromWasm(ptr, len) {
      return cachedTextDecoder.decode(getUint8Memory().subarray(ptr, ptr + len));
  }

  function addHeapObject(obj) {
      if (heap_next === heap.length) heap.push(heap.length + 1);
      const idx = heap_next;
      heap_next = heap[idx];

      heap[idx] = obj;
      return idx;
  }

  __exports.__wbindgen_string_new = function(p, l) { return addHeapObject(getStringFromWasm(p, l)); };

  __exports.__wbindgen_json_parse = function(ptr, len) { return addHeapObject(JSON.parse(getStringFromWasm(ptr, len))); };

  __exports.__wbindgen_throw = function(ptr, len) {
      throw new Error(getStringFromWasm(ptr, len));
  };

  function freeXLSX(ptr) {

      wasm.__wbg_xlsx_free(ptr);
  }
  /**
  */
  class XLSX {

      static __wrap(ptr) {
          const obj = Object.create(XLSX.prototype);
          obj.ptr = ptr;

          return obj;
      }

      free() {
          const ptr = this.ptr;
          this.ptr = 0;
          freeXLSX(ptr);
      }

      /**
      * @param {Uint8Array} data
      * @returns {XLSX}
      */
      static new(data) {
          const ptr0 = passArray8ToWasm(data);
          const len0 = WASM_VECTOR_LEN;
          return XLSX.__wrap(wasm.xlsx_new(ptr0, len0));
      }
      /**
      * @returns {any}
      */
      get_styles() {
          return takeObject(wasm.xlsx_get_styles(this.ptr));
      }
      /**
      * @returns {any[]}
      */
      get_sheets() {
          const retptr = globalArgumentPtr();
          wasm.xlsx_get_sheets(retptr, this.ptr);
          const mem = getUint32Memory();
          const rustptr = mem[retptr / 4];
          const rustlen = mem[retptr / 4 + 1];

          const realRet = getArrayJsValueFromWasm(rustptr, rustlen).slice();
          wasm.__wbindgen_free(rustptr, rustlen * 4);
          return realRet;

      }
      /**
      * @param {string} sheet_name
      * @returns {any}
      */
      get_sheet_data(sheet_name) {
          const ptr0 = passStringToWasm(sheet_name);
          const len0 = WASM_VECTOR_LEN;
          return takeObject(wasm.xlsx_get_sheet_data(this.ptr, ptr0, len0));
      }
  }
  __exports.XLSX = XLSX;

  __exports.__wbindgen_object_drop_ref = function(i) { dropObject(i); };

  function init(module_or_path, maybe_memory) {
      let result;
      const imports = { './xlsx_export': __exports };
      if (module_or_path instanceof URL || typeof module_or_path === 'string' || module_or_path instanceof Request) {

          const response = fetch(module_or_path);
          if (typeof WebAssembly.instantiateStreaming === 'function') {
              result = WebAssembly.instantiateStreaming(response, imports)
              .catch(e => {
                  console.warn("`WebAssembly.instantiateStreaming` failed. Assuming this is because your server does not serve wasm with `application/wasm` MIME type. Falling back to `WebAssembly.instantiate` which is slower. Original error:\n", e);
                  return response
                  .then(r => r.arrayBuffer())
                  .then(bytes => WebAssembly.instantiate(bytes, imports));
              });
          } else {
              result = response
              .then(r => r.arrayBuffer())
              .then(bytes => WebAssembly.instantiate(bytes, imports));
          }
      } else {

          result = WebAssembly.instantiate(module_or_path, imports)
          .then(instance => {
              return { instance, module: module_or_path };
          });
      }
      return result.then(({instance, module}) => {
          wasm = instance.exports;
          init.__wbindgen_wasm_module = module;

          return wasm;
      });
  }

  self.wasm_bindgen = Object.assign(init, __exports);

  })();

  onmessage = function(e) {
      const config = e.data;

      if (e.data.type === "convert") {
          const data = e.data.data;
          if (data instanceof File){
              const reader =  new FileReader();
              reader.readAsArrayBuffer(data);
              reader.onload = (e) => doConvert(new Int8Array(e.target.result), config);
          } else {
              doConvert(data, config);
          }
      }
  };


  let XLSX = null;
  function doConvert(input, config){
      const path = config.wasmPath || "https://cdn.dhtmlx.com/libs/excel2json/1.0/lib.wasm";
      const getStyles = config.styles === undefined ? true : config.styles;

      if (XLSX) {
          const xlsx = XLSX.new(input);
          const styles = getStyles ? xlsx.get_styles() : null;

          let sheetsData;
          if (config.sheet) {
              const data = xlsx.get_sheet_data(sheet);
              sheetsData = [data];
          } else {
              const sheets = xlsx.get_sheets();
              sheetsData = sheets.map(name => xlsx.get_sheet_data(name));
          }

          postMessage({
              uid: config.uid || (new Date()).valueOf(),
              type: "ready",
              data: sheetsData,
              styles
          });
      } else {
          wasm_bindgen(path).then(() => {
              XLSX = wasm_bindgen.XLSX;
              doConvert(input, config);
          }).catch(e => console.log(e));
      }
  }

}());
