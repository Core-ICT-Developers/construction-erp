import { ISpreadsheet } from "../types";
export declare class FileProxy {
    private _xlsxWorker;
    private _spreadsheet;
    constructor(spreadsheet: ISpreadsheet);
    load(type?: "xlsx" | "json", url?: string): Promise<any>;
    private _getXlsxWorker;
    private _getFile;
}
