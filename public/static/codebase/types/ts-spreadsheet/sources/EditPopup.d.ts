import { IFormula } from "../../muon";
export default class EditPopup {
    private _suggestCallback;
    private _popup;
    private _list;
    private _cursorStart;
    private _startTrim;
    constructor(cb: Function);
    isVisible(): boolean;
    hide(): void;
    destructor(): void;
    navigate(key: "arrowDown" | "arrowUp" | "enter"): boolean;
    show(val: string, input: HTMLInputElement, fm: IFormula, maxHeight?: number, cursor?: number): void;
    private _insertSuggest;
    private _getPopup;
}
