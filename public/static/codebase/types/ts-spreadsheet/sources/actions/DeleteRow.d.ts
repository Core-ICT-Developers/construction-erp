import { IAction, IActionConfig } from "./../types";
export declare class DeleteRow implements IAction {
    config: IActionConfig;
    private _row;
    private _cells;
    private _sheetName;
    private _prevRows;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
