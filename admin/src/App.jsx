import { __ } from '@wordpress/i18n';
import Insights from './Insights';
import SearchBar from './SearchBar';

import { useWidgets } from './context';
import WidgetCard from './WidgetCard';

// ── Widget Area ──────────────────────────────────────────
function WidgetArea({ area }) {
  const widgets = area.widgets ?? [];

  const sourceLabel = area.source === 'elementor' ? 'Elementor' : 'WordPress';

  return (
    <div className="wisema-widget-area">
      <h2>
        {area.area_name ?? area.area}
        <span className="wisema-area-source-badge">{sourceLabel}</span>
      </h2>

      <div className="wisema-widget-area-content">
        {widgets.length === 0 ? (
          <p style={{ color: '#aaa', fontSize: 13, padding: '6px 0' }}>
            No active widgets
          </p>
        ) : (
          widgets.map((w) => <WidgetCard key={w.id} widget={w} area={area} />)
        )}
      </div>
    </div>
  );
}

// ── Root App ───────────────────────────────────────────────
export default function App() {
  const { widgets, activeTab, setActiveTab, searchQuery } = useWidgets();

  const safeWidgets = Array.isArray(widgets) ? widgets : [];

  // 🔍 FILTER LOGIC
  const filteredWidgets = safeWidgets.map((area) => {
    const q = searchQuery.toLowerCase();

    const filtered = area.widgets.filter((w) => {
      return (
        w.name?.toLowerCase().includes(q) ||
        w.type?.toLowerCase().includes(q) ||
        area.area_name?.toLowerCase().includes(q)
      );
    });

    return {
      ...area,
      widgets: filtered,
    };
  });

  return (
    <>
      <div className="wisema-banner">
        <div className="wisema-logo">
          <svg
            width="90"
            height="90"
            viewBox="0 0 512 512"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <rect width="512" height="512" rx="120" fill="#D60413" />

            <rect
              x="110"
              y="110"
              width="292"
              height="292"
              rx="4"
              stroke="white"
              strokeWidth="18"
            />

            <line
              x1="256"
              y1="110"
              x2="256"
              y2="402"
              stroke="white"
              strokeWidth="18"
            />

            <line
              x1="110"
              y1="256"
              x2="402"
              y2="256"
              stroke="white"
              strokeWidth="18"
            />

            <path
              d="M165 180L185 240L205 180L225 240L245 180"
              stroke="#fff"
              strokeWidth="22"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
          </svg>
        </div>

        <div className="wisema--content">
          <h2>{__('Welcome to Wise Widget Manager', 'wise-widget-manager')}</h2>
          {__(
            'Wise Widget Manager brings all Classic widgets into one simple dashboard, making it easy to enable, disable, or remove widgets without jumping between screens.',
            'wise-widget-manager'
          )}
        </div>
      </div>

      <div className="wisema-tabs">
        <button
          className={activeTab === 'widget' ? 'active' : ''}
          onClick={() => setActiveTab('widget')}
        >
          {__('Manage Widgets', 'wise-widget-manager')}
        </button>

        <button
          className={activeTab === 'analyzer' ? 'active' : ''}
          onClick={() => setActiveTab('analyzer')}
        >
          {__('Insights', 'wise-widget-manager')}
        </button>
      </div>

      {activeTab === 'widget' && <SearchBar />}

      <div className="wisema-widget-manager">
        {activeTab === 'widget' && (
          <div className="wisema-widget-left">
            {safeWidgets.length === 0 && (
              <p style={{ color: '#aaa', fontSize: 13 }}>
                {__(
                  'Loading widget areas... or this theme doesnot have widget area',
                  'wise-widget-manager'
                )}
              </p>
            )}

            {filteredWidgets.map((area) => {
              if (!area) return null;

              return <WidgetArea key={area.area} area={area} />;
            })}
          </div>
        )}

        {activeTab === 'analyzer' && <Insights />}
      </div>
    </>
  );
}
