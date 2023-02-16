import { IEditor } from "../types";
export declare class SelectEditor implements IEditor {
    protected _handlers: {
        [key: string]: (...args: any[]) => void;
    };
    protected _config: any;
    protected _input: HTMLInputElement;
    private _list;
    private _data;
    private _popup;
    private _events;
    private _endEdit;
    private edit;
    constructor(cb: any, events: any);
    endEdit(): void;
    setValue(data: any): void;
    toHTML(conf: any): any[];
    private _filter;
    protected _showPopup(): void;
    protected _initHandlers(): void;
}
