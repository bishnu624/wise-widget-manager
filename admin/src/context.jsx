import { createContext, useContext, useEffect, useState } from 'react';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
const Ctx = createContext();
export const useWidgets = () => useContext(Ctx);

export const Provider = ({ children }) => {
  const [widgets, setWidgets] = useState([]);
  const [selectedWidgetId, setSelectedWidgetId] = useState(null);
  const [sidebarHtml, setSidebarHtml] = useState('');
  const [activeTab, setActiveTab] = useState('widget');
  const [notifications, setNotifications] = useState([]);

  const [searchQuery, setSearchQuery] = useState('');
  // key: widgetId  →  { devices: {desktop,tablet,mobile}, users: {logged_in,logged_out} }

  // ── Load widget list ───────────────────────────────────────
  const load = async () => {
    try {
      const data = await apiFetch({
        url: `${wisewima.apiUrl}/wisewima/v1/widgets`,
        method: 'GET',
        headers: { 'X-WP-Nonce': wisewima.nonce },
      });
      setWidgets(Array.isArray(data) ? data : []);
    } catch (e) {
      console.error('Load failed:', e);
    }
  };

  // ── Toggle widget enabled/disabled ────────────────────────
  const toggle = async (id, enabled) => {
    setWidgets((prev) =>
      prev.map((area) => ({
        ...area,
        widgets: area.widgets.map((w) =>
          w.id === id ? { ...w, enabled: !enabled } : w
        ),
      }))
    );
    try {
      await apiFetch({
        url: `${wisewima.apiUrl}/wisewima/v1/toggle`,
        method: 'POST',
        headers: { 'X-WP-Nonce': wisewima.nonce },
        data: { id, enabled: !enabled },
      });

      let succMsg = __('Widget updated successfully');
      notify(succMsg);
    } catch (e) {
      console.error('Toggle failed:', e);
      setWidgets((prev) =>
        prev.map((area) => ({
          ...area,
          widgets: area.widgets.map((w) =>
            w.id === id ? { ...w, enabled } : w
          ),
        }))
      );
    }
  };

  // ── Remove widget ──────────────────────────────────────────
  const remove = async (id) => {
    await apiFetch({
      url: `${wisewima.apiUrl}/wisewima/v1/remove`,
      method: 'POST',
      headers: { 'X-WP-Nonce': wisewima.nonce },
      data: { id },
    });
    if (selectedWidgetId === id) {
      setSelectedWidgetId(null);
    }
    await load();
    let removedMsg = __('Widget updated successfully');
    notify(removedMsg);
  };

  // ── Notifications ──────────────────────────────────────────
  const notify = (message, type = 'success') => {
    const id = Date.now();
    setNotifications((prev) => [...prev, { id, message, type }]);
    setTimeout(() => {
      setNotifications((prev) => prev.filter((n) => n.id !== id));
    }, 5000);
  };

  useEffect(() => {
    load();
  }, []);

  return (
    <Ctx.Provider
      value={{
        widgets,
        toggle,
        remove,
        selectedWidgetId,
        setSelectedWidgetId,
        activeTab,
        setActiveTab,
        notifications,
        notify,
        searchQuery,
        setSearchQuery,
      }}
    >
      {children}
    </Ctx.Provider>
  );
};
