import { IAction, IActionConfig } from "./../types";
export declare class GroupAction implements IAction {
    config: IActionConfig;
    private _actions;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
