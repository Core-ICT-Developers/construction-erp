import { IAction, IActionConfig } from "./../types";
export declare class LockCell implements IAction {
    config: IActionConfig;
    private _store;
    private _page;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
