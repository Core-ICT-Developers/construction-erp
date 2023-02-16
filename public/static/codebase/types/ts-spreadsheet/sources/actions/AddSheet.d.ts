import { IAction, IActionConfig } from "../types";
export declare class AddSheet implements IAction {
    config: IActionConfig;
    private _id;
    private _store;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
