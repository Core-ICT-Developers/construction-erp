import { IAction, IActionConfig } from "./../types";
export declare class GroupColAction implements IAction {
    config: IActionConfig;
    private _actions;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
