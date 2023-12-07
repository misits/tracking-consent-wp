import { useState, useEffect } from "react";

function ConsentForm() {
  const [consent, setConsent] = useState(false);

  const [theme, setTheme] = useState(() => {
    // Get theme from local storage or set to default
    const savedTheme = localStorage.getItem("theme");
    return savedTheme ? JSON.parse(savedTheme) : {};
  });
  const [consentData, setConsentData] = useState(() => {
    // Get theme from local storage or set to default
    const savedConsentData = localStorage.getItem("consent");
    return savedConsentData ? JSON.parse(savedConsentData) : {};
  });

  const getConsentCookie = () => {
    // check mtm_cookie_consent cookie for consent
    const cookie = document.cookie.split(";").find((cookie) => {
      return cookie.trim().startsWith("mtm_cookie_consent=");
    });

    // if cookie is found, return the value
    if (cookie) {
      setConsent(cookie.split("=")[1] ? true : false);
    }
    return consent;
  };

  const setConsentCookie = (userConsent) => {
    if (userConsent) {
      _paq.push(["setCookieConsentGiven"]);
      _paq.push(["rememberCookieConsentGiven"]);
      localStorage.setItem("consent-given", "accepted"); // Save to local storage
    } else {
      _paq.push(["forgetCookieConsentGiven"]);
      localStorage.setItem("consent-given", "refused"); // Save to local storage

      // Hide the cookie banner
      const element = document.getElementById(`wp-tracking-consent-front`);
      element.classList.remove("visible");
      element.classList.add("hidden");
      setTimeout(() => {
        element.classList.add("none");
      }, 100);
    }

    displayConsentForm();
  };

  const handleConsent = (userConsent) => {
    setConsentCookie(userConsent);
  };

  // Logic to display or hide the consent form based on cookie
  const displayConsentForm = () => {
    const element = document.getElementById(`wp-tracking-consent-front`);
    if (!element) return;
    if (!getConsentCookie()) {
      // await 1 second before showing the consent form
      setTimeout(() => {
        element.classList.add("visible");
      }, 100);
    } else {
      element.classList.remove("visible");

      setTimeout(() => {
        element.classList.add("hidden");
      }, 100);
      setTimeout(() => {
        element.classList.add("none");
      }, 200);
    }
  };

  useEffect(() => {
    // Fetch theme settings only once on component mount
    fetch("/wp-json/wp-tracking-consent/v1/settings", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        setTheme(data.theme);
        localStorage.setItem("theme", JSON.stringify(data.theme)); // Save to local storage
        // update css variables
        const root = document.documentElement;
        root.style.setProperty(
          "--color-background",
          theme.background_color || "#fff"
        );
        root.style.setProperty("--color-text", theme.text_color || "#000");
        root.style.setProperty(
          "--color-primary",
          theme.primary_color || "#000"
        );
        root.style.setProperty(
          "--consent-radius",
          theme.radius + "px" || "0px"
        );

        setConsentData(data.consent);
        localStorage.setItem("consent", JSON.stringify(data.consent)); // Save to local storage
      })
      .catch((error) => {
        console.error(error);
      });
  }, []);

  useEffect(() => {
    if (localStorage.getItem("consent-given")) {
      return;
    }

    displayConsentForm();

    return () => {
      displayConsentForm();
    };
  }, [consent]);

  if (!consentData && !theme) return <></>;

  return (
    <form
      id="consent-form"
      className={`consent-form ${theme ? theme.banner_class : ""}`}
      onSubmit={(e) => e.preventDefault()}
    >
      <div className="consent-form__description">
        {consentData && (
          <div dangerouslySetInnerHTML={{ __html: consentData.content }}></div>
        )}
      </div>
      <div className="consent-form__actions">
        <div className="form-fields">
          <button
            className={`button button--primary ${
              theme ? theme.button_class : ""
            }`}
            type="submit"
            onClick={() => handleConsent(true)}
          >
            {consentData && consentData.accept_text
              ? consentData.accept_text
              : "Accept"}
          </button>
        </div>
        <div className="form-fields">
          <button
            className={`button button--secondary ${
              theme ? theme.button_class : ""
            } `}
            type="submit"
            onClick={() => handleConsent(false)}
          >
            {consentData && consentData.decline_text
              ? consentData.decline_text
              : "Reject"}
          </button>
        </div>
        {consentData && (
          <div className="form-fields">
            <a
              href={consentData.read_more}
              className={`button button--secondary button--icon ${
                theme ? theme.button_class : ""
              } `}
            >
              
              En savoir plus
              <svg
                width="18"
                height="18"
                version="1.1"
                viewBox="0 0 1200 1200"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
              >
                <g>
                  <path d="m1099.3 531.6-7.0195-16.309-24.371 2.6523h-0.003906c-13.105 1.0312-26.105 3.1133-38.879 6.2148-12.355 3.6484-24.395 8.2969-36 13.895-13.914 7.6211-29.215 12.363-45 13.945-15.801-1.582-31.121-6.3281-45.047-13.957-11.586-5.5938-23.605-10.238-35.941-13.883-12.93-3.1406-26.094-5.2422-39.359-6.2773-15.418-0.37109-30.605-3.8008-44.688-10.09-12.621-9.1602-23.289-20.742-31.379-34.07-7.3906-10.859-15.648-21.102-24.699-30.621-9.5156-9.0469-19.754-17.301-30.609-24.688-13.359-8.1094-24.961-18.812-34.117-31.473-6.2617-14.055-9.6758-29.211-10.043-44.594-1.0352-13.262-3.1328-26.418-6.2656-39.348-3.6484-12.355-8.2969-24.395-13.895-36-7.625-13.914-12.371-29.215-13.957-45 1.582-15.801 6.3281-31.121 13.957-45.047 5.582-11.555 10.215-23.543 13.859-35.844 2.2695-8.7422 3.9062-17.641 4.8945-26.617l2.5312-20.891-20.398-5.2812c-10.723-2.8242-21.758-4.2773-32.844-4.3203-30.031 1.9062-59.309 10.223-85.859 24.383-13.387 6.5156-27.273 11.957-41.52 16.273-15 3.4961-30.246 5.8398-45.602 7.0195-29.543 0.98438-58.535 8.2852-85.02 21.41-24.801 16.426-45.75 38.027-61.402 63.32-8.5742 12.641-18.137 24.578-28.598 35.703-11.105 10.426-23.02 19.961-35.629 28.512-25.27 15.656-46.852 36.594-63.262 61.379-13.137 26.488-20.449 55.48-21.445 85.031-1.1758 15.355-3.5156 30.602-7.0078 45.602-4.3203 14.238-9.7617 28.113-16.273 41.496-14.164 26.555-22.477 55.836-24.383 85.871 1.9062 30.031 10.223 59.309 24.383 85.859 6.5156 13.387 11.957 27.273 16.273 41.52 3.4961 15 5.8398 30.246 7.0195 45.602 0.98438 29.543 8.2852 58.531 21.41 85.02 16.426 24.801 38.027 45.75 63.32 61.402 12.641 8.5742 24.578 18.137 35.703 28.598 10.426 11.105 19.961 23.02 28.512 35.629 15.656 25.27 36.594 46.852 61.379 63.262 26.488 13.141 55.48 20.449 85.031 21.445 15.355 1.1758 30.602 3.5156 45.602 7.0078 14.238 4.3203 28.113 9.7617 41.496 16.273 26.555 14.164 55.836 22.477 85.871 24.383 30.031-1.9062 59.309-10.223 85.859-24.383 13.387-6.5156 27.273-11.957 41.52-16.273 15-3.4961 30.246-5.8398 45.602-7.0195 29.543-0.98438 58.531-8.2852 85.02-21.41 24.801-16.426 45.75-38.027 61.402-63.32 8.5742-12.641 18.137-24.578 28.598-35.703 11.105-10.426 23.02-19.961 35.629-28.512 25.27-15.656 46.852-36.594 63.262-61.379 13.137-26.488 20.449-55.48 21.445-85.031 1.1758-15.355 3.5156-30.602 7.0078-45.602 4.3203-14.238 9.7617-28.113 16.273-41.496 14.164-26.555 22.477-55.836 24.383-85.871-0.88672-23.699-6.5781-46.969-16.727-68.398zm-51.434 134.54c-7.6133 15.75-13.93 32.098-18.875 48.875-4.1797 17.414-6.9766 35.129-8.3633 52.98-0.61328 22.805-5.8125 45.25-15.289 66-13.273 18.699-30.219 34.496-49.801 46.43-14.711 10.027-28.586 21.23-41.484 33.504-12.309 12.918-23.543 26.816-33.598 41.555-11.926 19.602-27.723 36.562-46.43 49.848-20.754 9.4648-43.199 14.652-66 15.254-17.844 1.3984-35.551 4.207-52.957 8.3984-16.785 4.9453-33.133 11.258-48.887 18.875-20.434 11.184-42.957 18.039-66.156 20.137-23.199-2.1055-45.715-8.9688-66.145-20.16-15.75-7.6133-32.098-13.93-48.875-18.875-17.414-4.1797-35.129-6.9766-52.98-8.3633-22.805-0.61328-45.25-5.8125-66-15.289-18.699-13.273-34.496-30.219-46.43-49.801-10.027-14.711-21.23-28.586-33.504-41.484-12.918-12.309-26.816-23.543-41.555-33.598-19.602-11.926-36.562-27.723-49.848-46.43-9.4648-20.754-14.652-43.199-15.254-66-1.3984-17.844-4.207-35.551-8.3984-52.957-4.9453-16.785-11.258-33.133-18.875-48.887-11.184-20.434-18.039-42.957-20.137-66.156 2.1055-23.199 8.9688-45.715 20.16-66.145 7.6133-15.75 13.93-32.098 18.875-48.875 4.1797-17.414 6.9766-35.129 8.3633-52.98 0.61328-22.805 5.8125-45.25 15.289-66 13.273-18.699 30.219-34.496 49.801-46.43 14.711-10.027 28.586-21.23 41.484-33.504 12.309-12.918 23.543-26.816 33.598-41.555 11.926-19.602 27.723-36.562 46.43-49.848 20.754-9.4648 43.199-14.652 66-15.254 17.844-1.3984 35.551-4.207 52.957-8.3984 16.785-4.9453 33.133-11.258 48.887-18.875 20.027-10.855 41.984-17.695 64.633-20.137-2.9414 8.5898-6.3711 17.004-10.273 25.199-10.672 20.035-16.883 42.141-18.203 64.801 1.3203 22.66 7.5312 44.766 18.203 64.801 4.4961 9.2695 8.2734 18.871 11.305 28.715 2.4219 10.457 4.0234 21.086 4.8008 31.789 0.79688 22.148 6.3398 43.871 16.246 63.695 12.352 18.727 28.602 34.559 47.641 46.414 8.7344 5.9023 16.992 12.477 24.695 19.672 7.1953 7.7031 13.777 15.961 19.68 24.695 11.836 19.016 27.637 35.254 46.32 47.602 19.855 9.9375 41.609 15.492 63.793 16.297 10.711 0.78906 21.344 2.4141 31.801 4.8594 9.8438 3.0234 19.438 6.8008 28.703 11.293 20.039 10.66 42.152 16.859 64.812 18.168 22.66-1.3203 44.766-7.5312 64.801-18.203 9.2617-4.4922 18.852-8.2656 28.691-11.293 6.6328-1.6719 13.371-2.875 20.172-3.6016 3.918 10.609 6.0586 21.793 6.3359 33.098-2.1055 23.199-8.9688 45.715-20.16 66.145z" />
                  <path d="m408 276h60v60h-60z" />
                  <path d="m720 204h60v60h-60z" />
                  <path d="m588 888h60v60h-60z" />
                  <path d="m324 792h60v60h-60z" />
                  <path d="m540 624h60v60h-60z" />
                  <path d="m846.76 384.67 143.51 63.672c10.621 4.7109 22.688 4.9844 33.512 0.75781 10.828-4.2266 19.516-12.602 24.133-23.266 4.6211-10.664 4.7891-22.73 0.46875-33.52l-49.633-123.88c-4.8242-12.059-14.781-21.332-27.148-25.297-12.367-3.9648-25.859-2.2031-36.797 4.8008l-93.875 60.238c-8.8672 5.6914-15.371 14.406-18.309 24.523-2.9336 10.121-2.1055 20.961 2.3359 30.516 4.4453 9.5508 12.199 17.172 21.828 21.449zm109.43-93.422 43.586 108.8-126-55.922z" />
                  <path d="m336 480c-22.277 0-43.645 8.8516-59.398 24.602-15.75 15.754-24.602 37.121-24.602 59.398s8.8516 43.645 24.602 59.398c15.754 15.75 37.121 24.602 59.398 24.602s43.645-8.8516 59.398-24.602c15.75-15.754 24.602-37.121 24.602-59.398-0.027344-22.27-8.8867-43.617-24.633-59.367-15.75-15.746-37.098-24.605-59.367-24.633zm0 120c-9.5469 0-18.703-3.793-25.457-10.543-6.75-6.7539-10.543-15.91-10.543-25.457s3.793-18.703 10.543-25.457c6.7539-6.75 15.91-10.543 25.457-10.543s18.703 3.793 25.457 10.543c6.75 6.7539 10.543 15.91 10.543 25.457s-3.793 18.703-10.543 25.457c-6.7539 6.75-15.91 10.543-25.457 10.543z" />
                  <path d="m816 696c-19.094 0-37.41 7.5859-50.91 21.09-13.504 13.5-21.09 31.816-21.09 50.91s7.5859 37.41 21.09 50.91c13.5 13.504 31.816 21.09 50.91 21.09s37.41-7.5859 50.91-21.09c13.504-13.5 21.09-31.816 21.09-50.91-0.019531-19.09-7.6094-37.391-21.109-50.891s-31.801-21.09-50.891-21.109zm0 96c-6.3633 0-12.469-2.5273-16.969-7.0312-4.5039-4.5-7.0312-10.605-7.0312-16.969s2.5273-12.469 7.0312-16.969c4.5-4.5039 10.605-7.0312 16.969-7.0312s12.469 2.5273 16.969 7.0312c4.5039 4.5 7.0312 10.605 7.0312 16.969s-2.5273 12.469-7.0312 16.969c-4.5 4.5039-10.605 7.0312-16.969 7.0312z" />
                </g>
              </svg>
            </a>
          </div>
        )}
      </div>
    </form>
  );
}

export default ConsentForm;