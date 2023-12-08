import { useState, useEffect } from "react";

function MatomoGraph() {
  const [url, setUrl] = useState("");
  const [siteID, setSiteID] = useState("");
  const [matomoUrl, setMatomoUrl] = useState("");
  const [tokenAuth, setTokenAuth] = useState("anonymous");

  useEffect(() => {
    const data = JSON.parse(
      document.querySelector("#wp-tracking-consent-admin").dataset.consent
    );

    setSiteID(data.site_id);
    setMatomoUrl(data.matomo_url);
    if (data.token_auth.length) setTokenAuth(data.token_auth);

    if (!matomoUrl.length && !siteID.length) return;
    
    // set the url
    setUrl(
      `${matomoUrl}/index.php?module=Widgetize&action=iframe&moduleToWidgetize=Dashboard&actionToWidgetize=index&idSite=${siteID}&period=day&date=today&disableLink=1&widget=1&token_auth=${tokenAuth}`
    );
  }, [siteID, matomoUrl]);

  if (!url.length) {
    return (
      <>
        <div className="wp-tracking-consent__header">
          <h2 className="wp-tracking-consent__header__title">Missing data</h2>
          <p>Please check if all required fields are filed in settings tab.</p>
        </div>
      </>
    );
  }

  return (
    <>
      <iframe
        src={url}
        frameBorder="0"
        marginHeight="0"
        marginWidth="0"
        width="100%"
        height="100%"
      ></iframe>
    </>
  );
}

export default MatomoGraph;
