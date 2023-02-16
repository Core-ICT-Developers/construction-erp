import { IAction, IActionConfig } from "../types";
export declare class Clear implements IAction {
    config: IActionConfig;
    private _data;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
