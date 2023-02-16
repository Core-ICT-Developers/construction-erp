import { IAction, IActionConfig } from "../types";
export declare class ClearSheet implements IAction {
    config: IActionConfig;
    private _data;
    private _store;
    private _page;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
