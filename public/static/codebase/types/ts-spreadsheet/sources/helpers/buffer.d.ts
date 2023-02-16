import { IGrid } from "../../../ts-grid";
import { DataPage, DataStore } from "../../../muon";
import { IBufferStruct, IExecuteConfig, ISpreadsheet } from "./../types";
export declare class BufferManager {
    private _buffer;
    private _spreadsheet;
    private _grid;
    private _callAction;
    private _activePage;
    private _math;
    private _dataStore;
    private _prevCopyPage;
    constructor(spreadsheet: ISpreadsheet, grid: IGrid, callAction: (config: IExecuteConfig) => void, page: DataPage, dataStore: DataStore);
    store(operation: IBufferStruct["operation"], col?: any, row?: any): void;
    paste(): void;
    getStruct(): IBufferStruct;
    private _setHandlers;
}
