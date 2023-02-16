import { IEditor } from "../types";
export declare class DateEditor implements IEditor {
    protected _handlers: {
        [key: string]: (...args: any[]) => void;
    };
    protected _config: any;
    protected _input: HTMLInputElement;
    private _calendar;
    private _popup;
    private _events;
    private _endEdit;
    private edit;
    private _dateFormat;
    constructor(cb: any, dateFormat: any, events: any);
    endEdit(): void;
    setValue(value: number | string | boolean): void;
    hidePopup(): void;
    toHTML(conf: any): any;
    protected _showPopup(): void;
    protected _initHandlers(): void;
}
