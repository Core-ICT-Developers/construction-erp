import { mathFunction } from "../types";
declare type methodsHash = {
    [name: string]: mathFunction;
};
declare type methodGroupsHash = {
    [name: string]: methodsHash;
};
export declare const groups: methodGroupsHash;
export declare const methods: methodsHash;
export declare function addMethod(name: string, handler: mathFunction): void;
export {};
