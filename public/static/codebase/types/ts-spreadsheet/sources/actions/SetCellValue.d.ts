import { IAction, IActionConfig } from "./../types";
export declare class SetCellValue implements IAction {
    config: IActionConfig;
    private _page;
    private _page_rows;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
