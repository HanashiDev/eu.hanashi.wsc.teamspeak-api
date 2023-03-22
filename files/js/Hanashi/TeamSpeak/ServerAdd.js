var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || function (mod) {
    if (mod && mod.__esModule) return mod;
    var result = {};
    if (mod != null) for (var k in mod) if (k !== "default" && Object.prototype.hasOwnProperty.call(mod, k)) __createBinding(result, mod, k);
    __setModuleDefault(result, mod);
    return result;
};
define(["require", "exports", "WoltLabSuite/Core/Language"], function (require, exports, Language) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.ServerAdd = void 0;
    Language = __importStar(Language);
    class ServerAdd {
        constructor() {
            var _a;
            (_a = document
                .getElementById("queryType")) === null || _a === void 0 ? void 0 : _a.addEventListener("change", (ev) => this.changeQueryType(ev));
        }
        changeQueryType(e) {
            const target = e.target;
            if (target == null) {
                return;
            }
            let serverPortText = "wcf.page.teamspeakAdd.virtualServerPort";
            let serverPortDescriptionText = "wcf.page.teamspeakAdd.virtualServerPort.description";
            let passwordText = "wcf.page.teamspeakAdd.password";
            let serverPort = "9987";
            if (target.value == "http" || target.value == "https") {
                serverPortText = "wcf.page.teamspeakAdd.virtualServerID";
                serverPortDescriptionText =
                    "wcf.page.teamspeakAdd.virtualServerID.description";
                passwordText = "wcf.page.teamspeakAdd.apiKey";
                serverPort = "1";
            }
            const virtualServerPortContainer = document.getElementById("virtualServerPortContainer");
            if (virtualServerPortContainer != null) {
                const virtualServerPortLabel = virtualServerPortContainer.querySelector('label[for="virtualServerPort"]');
                if (virtualServerPortLabel != null) {
                    virtualServerPortLabel.innerHTML = Language.get(serverPortText);
                }
                const virtualServerPorDescriptionSmall = virtualServerPortContainer.querySelector("small:not(.innerError)");
                if (virtualServerPorDescriptionSmall != null) {
                    virtualServerPorDescriptionSmall.innerHTML = Language.get(serverPortDescriptionText);
                }
            }
            const passwordContainer = document.getElementById("passwordContainer");
            if (passwordContainer != null) {
                const passwordLabel = passwordContainer.querySelector('label[for="password"]');
                if (passwordLabel != null) {
                    passwordLabel.innerHTML = Language.get(passwordText);
                }
            }
            const virtualServerPort = document.getElementById("virtualServerPort");
            if (virtualServerPort != null) {
                virtualServerPort.value = serverPort;
            }
            const queryPortField = document.getElementById("queryPort");
            if (queryPortField != null) {
                if (target.value == "raw") {
                    queryPortField.value = "10011";
                }
                else if (target.value == "ssh") {
                    queryPortField.value = "10022";
                }
                else if (target.value == "http") {
                    queryPortField.value = "10080";
                }
                else if (target.value == "https") {
                    queryPortField.value = "10443";
                }
            }
        }
    }
    exports.ServerAdd = ServerAdd;
    exports.default = ServerAdd;
});
