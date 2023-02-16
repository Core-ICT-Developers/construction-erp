import { IRawCode, IFormula, IMathStore, cellId } from "../types";
import { DataPage } from "./page";
export declare type RangeInfo = {
    code: IRawCode;
    id: number;
};
declare type PageGetter = {
    (name: string | number): DataPage;
};
export declare class RangeStore {
    private _ranges;
    private _rangeOrder;
    private _counter;
    private _store;
    private _page;
    private _pageGetter;
    constructor(store: IMathStore, page: DataPage, pageGetter: PageGetter);
    get(name: string): IFormula;
    toId(name: string): cellId;
    add(name: string, text: string): void;
    remove(name: string): void;
    serialize(): [string, string][];
    parse(data: [string, string][]): void;
    _refresh(name: string): void;
    _next_id(): number;
    renamePage(o: string, n: string): void;
}
export {};
