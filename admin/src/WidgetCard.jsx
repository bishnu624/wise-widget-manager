import { useState } from 'react';
import { useWidgets } from './context';

import { Eye, EyeOff, Trash2, Search } from 'lucide-react';

const TYPE_BADGE = {
  classic: {
    label: 'Classic',
    bg: '#eef2ff',
    color: '#4338ca',
    border: '#c7d2fe',
  },
  block: {
    label: 'Gutenberg',
    bg: '#f0fdf4',
    color: '#15803d',
    border: '#bbf7d0',
  },
  elementor: {
    label: 'Elementor',
    bg: '#fff7ed',
    color: '#c2410c',
    border: '#fed7aa',
  },
  woocommerce: {
    label: 'WooCommerce',
    bg: '#fdf4ff',
    color: '#7e22ce',
    border: '#e9d5ff',
  },
  unknown: {
    label: 'Widget',
    bg: '#f9fafb',
    color: '#6b7280',
    border: '#e5e7eb',
  },
};

// ── Widget Card ────────────────────────────────────────────────
export default function WidgetCard({ widget, area }) {
  const { toggle, remove, preview, setSelectedWidgetId, selectedWidgetId } =
    useWidgets();

  const [settingsOpen] = useState(false);

  const isSelected = selectedWidgetId === widget.id;
  const badge = TYPE_BADGE[widget.type] ?? TYPE_BADGE.unknown;
  const isElementor = widget.type === 'elementor';

  const handlePreview = () => {
    setSelectedWidgetId(widget.id);
    if (preview) preview(widget);
  };

  return (
    <div
      className={`wisema-widget-card ${
        widget.enabled ? 'active' : 'inactivate'
      } ${settingsOpen ? 'wisema-widget-card--open' : ''}`}
      style={{
        borderColor: isSelected ? '#6366f1' : '#eee',
        borderWidth: isSelected ? 2 : 1,
        marginBottom: '5px',
      }}
    >
      <div className="wisema-widget-card-top">
        {/* INFO */}
        <div className="wisema-widget-info">
          <b>{widget.name}</b>

          <div className="wisema-widget-meta">
            <span className="wisema-widget-area-name">{area.area_name}</span>

            <span
              className="wisema-widget-type-badge"
              style={{
                background: badge.bg,
                color: badge.color,
                border: `1px solid ${badge.border}`,
              }}
            >
              {badge.label}
            </span>
          </div>
        </div>

        {/* ACTIONS */}
        <div className="wisema-widget-actions">
          {!isElementor && (
            <button
              className="wisema-btn-toggle"
              onClick={() => toggle(widget.id, widget.enabled)}
              title={widget.enabled ? 'Disable' : 'Enable'}
            >
              {widget.enabled ? <Eye size={18} /> : <EyeOff size={18} />}
            </button>
          )}

          {!isElementor && (
            <button
              className="wisema-btn-remove"
              onClick={() => {
                if (confirm(`Remove "${widget.name}"?`)) {
                  remove(widget.id);
                }
              }}
              title="Remove widget"
            >
              <Trash2 size={18} />
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
