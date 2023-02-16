import { IAction, IActionConfig } from "./../types";
export declare class DeleteColumn implements IAction {
    config: IActionConfig;
    private _index;
    private _cells;
    private _prevCols;
    private _sheetName;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
