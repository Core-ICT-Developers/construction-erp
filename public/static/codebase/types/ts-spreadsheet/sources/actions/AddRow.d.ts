import { IAction, IActionConfig } from "./../types";
export declare class AddRow implements IAction {
    config: IActionConfig;
    private _index;
    private _sheetName;
    private _prevRows;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
