import { IAction, IActionConfig } from "../types";
export declare class ResizeCol implements IAction {
    config: IActionConfig;
    private _prevCols;
    private _colsAfterResize;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
