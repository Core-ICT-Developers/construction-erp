import { cellId } from "./types";
export declare function toId(r: number, c: number, p: number): cellId;
export declare function fromId(id: cellId): number[];
export declare function row(id: cellId): number;
export declare function column(id: cellId): number;
export declare function page(id: cellId): number;
export declare function eachRange(cId: cellId, tId: cellId, cb: (t: cellId) => void): void;
