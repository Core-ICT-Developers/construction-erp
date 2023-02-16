import { IAction, IActionConfig } from "../types";
export declare class SetValidation implements IAction {
    config: IActionConfig;
    private _page;
    private _prevState;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
