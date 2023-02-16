import { Store } from "../store";
import { DataPage } from "./page";
import { RangeStore } from "./ranges";
export declare class DataStore {
    private _pages;
    private _pageName2ID;
    private _pageNames;
    private _store;
    private _ranges;
    private _pCounter;
    private _parseContext;
    constructor();
    addPage(name: string): DataPage;
    renamePage(name: string, newName: string): void;
    removePage(name: string): void;
    getPage(name: string | number): DataPage;
    getRanges(): RangeStore;
    getStore(): Store;
}
