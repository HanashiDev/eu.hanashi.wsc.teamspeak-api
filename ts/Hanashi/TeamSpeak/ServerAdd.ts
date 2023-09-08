import * as Language from "WoltLabSuite/Core/Language";

export class ServerAdd {
  constructor() {
    document
      .getElementById("queryType")
      ?.addEventListener("change", (ev) => this.changeQueryType(ev));
  }

  protected changeQueryType(e: Event): void {
    const target: HTMLSelectElement | null =
      e.target as HTMLSelectElement | null;
    if (target == null) {
      return;
    }

    let serverPortText = "wcf.page.teamspeakAdd.virtualServerPort";
    let serverPortDescriptionText =
      "wcf.page.teamspeakAdd.virtualServerPort.description";
    let passwordText = "wcf.page.teamspeakAdd.password";
    let serverPort = "9987";

    if (target.value == "http" || target.value == "https") {
      serverPortText = "wcf.page.teamspeakAdd.virtualServerID";
      serverPortDescriptionText =
        "wcf.page.teamspeakAdd.virtualServerID.description";
      passwordText = "wcf.page.teamspeakAdd.apiKey";
      serverPort = "1";
    }

    const virtualServerPortContainer = document.getElementById(
      "virtualServerPortContainer",
    );
    if (virtualServerPortContainer != null) {
      const virtualServerPortLabel = virtualServerPortContainer.querySelector(
        'label[for="virtualServerPort"]',
      );
      if (virtualServerPortLabel != null) {
        virtualServerPortLabel.innerHTML = Language.getPhrase(serverPortText);
      }
      const virtualServerPorDescriptionSmall =
        virtualServerPortContainer.querySelector("small:not(.innerError)");
      if (virtualServerPorDescriptionSmall != null) {
        virtualServerPorDescriptionSmall.innerHTML = Language.getPhrase(
          serverPortDescriptionText,
        );
      }
    }

    const passwordContainer = document.getElementById("passwordContainer");
    if (passwordContainer != null) {
      const passwordLabel = passwordContainer.querySelector(
        'label[for="password"]',
      );
      if (passwordLabel != null) {
        passwordLabel.innerHTML = Language.getPhrase(passwordText);
      }
    }

    const virtualServerPort = document.getElementById(
      "virtualServerPort",
    ) as HTMLInputElement | null;
    if (virtualServerPort != null) {
      virtualServerPort.value = serverPort;
    }

    const queryPortField: HTMLInputElement | null = document.getElementById(
      "queryPort",
    ) as HTMLInputElement | null;
    if (queryPortField != null) {
      if (target.value == "raw") {
        queryPortField.value = "10011";
      } else if (target.value == "ssh") {
        queryPortField.value = "10022";
      } else if (target.value == "http") {
        queryPortField.value = "10080";
      } else if (target.value == "https") {
        queryPortField.value = "10443";
      }
    }
  }
}

export default ServerAdd;
