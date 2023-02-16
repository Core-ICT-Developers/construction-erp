import { IEditor } from "../types";
export declare class TimeEditor implements IEditor {
    protected _handlers: {
        [key: string]: (...args: any[]) => void;
    };
    protected _config: any;
    protected _input: HTMLInputElement;
    private _timepicker;
    private _popup;
    private _events;
    private _isFromSet;
    private _initialDate;
    constructor(_cb: any, timeFormat: any, events: any);
    endEdit(): void;
    setValue(value: number | string): void;
    hidePopup(): void;
    toHTML(conf: any): any;
    protected _showPopup(): void;
    protected _initHandlers(): void;
    protected _initTimePickerEvents(): void;
}
