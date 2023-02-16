import { cellValue } from "../types";
export declare function IF(check: cellValue, pos: cellValue, neg: cellValue): cellValue;
export declare function AND(...rest: cellValue[]): boolean;
export declare function NOT(value: cellValue): boolean;
export declare function OR(...rest: cellValue[]): boolean;
export declare function CHOOSE(num: cellValue, ...rest: cellValue[]): cellValue;
export declare function FALSE(): boolean;
export declare function TRUE(): boolean;
