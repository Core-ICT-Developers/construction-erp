import { ISpreadsheetConfig } from "../types";
export declare function getMenuStruct(config: ISpreadsheetConfig): ({
    id: string;
    open: boolean;
    value: string;
    items: {
        id: string;
        value: string;
        icon: string;
        items: {
            id: string;
            value: string;
            icon: string;
        }[];
    }[];
} | {
    id: string;
    value: string;
    items: ({
        id: string;
        value: string;
        icon: string;
        type?: undefined;
        items?: undefined;
    } | {
        type: string;
        id?: undefined;
        value?: undefined;
        icon?: undefined;
        items?: undefined;
    } | {
        id: string;
        value: string;
        icon: string;
        items: {
            id: string;
            value: string;
        }[];
        type?: undefined;
    })[];
    open?: undefined;
} | {
    id: string;
    value: string;
    items: ({
        id: string;
        value: string;
        icon: string;
        type?: undefined;
        items?: undefined;
    } | {
        type: string;
        id?: undefined;
        value?: undefined;
        icon?: undefined;
        items?: undefined;
    } | {
        id: string;
        value: string;
        items: {
            id: string;
            value: string;
            twoState: boolean;
            group: string;
            icon: string;
        }[];
        icon?: undefined;
        type?: undefined;
    } | {
        id: string;
        type: string;
        value: string;
        items: {
            id: string;
            css: string;
            twoState: boolean;
            group: string;
            html: string;
        }[];
        icon?: undefined;
    })[];
    open?: undefined;
} | {
    id: string;
    value: string;
    items: ({
        id: string;
        value: string;
        icon: string;
        items?: undefined;
    } | {
        id: string;
        value: string;
        icon: string;
        items: {
            id: string;
            value: string;
            icon: string;
        }[];
    })[];
    open?: undefined;
} | {
    id: string;
    value: string;
    open?: undefined;
    items?: undefined;
})[];
