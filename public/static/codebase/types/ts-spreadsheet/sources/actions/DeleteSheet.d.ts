import { IAction, IActionConfig } from "../types";
export declare class DeleteSheet implements IAction {
    config: IActionConfig;
    private _index;
    private _data;
    private _sheetMeta;
    private _store;
    private _deletedSheet;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
