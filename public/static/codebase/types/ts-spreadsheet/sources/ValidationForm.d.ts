export declare class ValidationForm {
    protected _handlers: {
        [key: string]: (...args: any[]) => void;
    };
    private _form;
    private _modal;
    private _focus;
    private _events;
    private _set;
    constructor(events: any, set: any);
    hide(): void;
    formValidation(val: any): any;
    show(cell: string, config?: any): void;
    protected _save(save: boolean): void;
    protected _initHandlers(): void;
}
