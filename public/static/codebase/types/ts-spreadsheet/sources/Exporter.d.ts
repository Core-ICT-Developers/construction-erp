import { IGrid } from "../../ts-grid";
import { ISpreadsheet } from "./types";
export declare class Exporter {
    private _spreadsheet;
    private _grid;
    private _xlsxWorker;
    constructor(spreadsheet: ISpreadsheet, grid: IGrid);
    xlsx(): void;
    json(): void;
    private _getXlsxWorker;
}
