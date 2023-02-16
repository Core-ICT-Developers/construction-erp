import { cellValue } from "../types";
declare function INDEX(a: cellValue[], row: cellValue, column: cellValue): cellValue;
declare function XMATCH(v: cellValue, a: cellValue[], mode: cellValue, search: cellValue): cellValue;
declare function XLOOKUP(v: cellValue, a: cellValue[], b: cellValue[], def: cellValue, mode: cellValue, search: cellValue): cellValue;
declare function LOOKUP(v: cellValue, a: cellValue[], b: cellValue[]): cellValue;
declare function HLOOKUP(v: cellValue, a: cellValue[], row: cellValue, mode: cellValue): cellValue;
declare function VLOOKUP(v: cellValue, a: cellValue[], column: cellValue, mode: cellValue): cellValue;
declare function MATCH(v: cellValue, a: cellValue[], mode: cellValue): cellValue;
declare const methods: {
    LOOKUP: typeof LOOKUP;
    HLOOKUP: typeof HLOOKUP;
    VLOOKUP: typeof VLOOKUP;
    XLOOKUP: typeof XLOOKUP;
    INDEX: typeof INDEX;
    MATCH: typeof MATCH;
    XMATCH: typeof XMATCH;
};
export default methods;
