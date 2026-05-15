import { useEffect, useState } from 'react';
import './SidebarInsights.css';

export default function SidebarInsights() {
  const [data, setData] = useState([]);

  const loadSidebarInsights = async () => {
    try {
      const res = await fetch(
        `${wisewima.apiUrl}/wisewima/v1/sidebar-insights`,
        {
          method: 'GET',
          headers: {
            'X-WP-Nonce': wisewima.nonce,
          },
        }
      );

      const data = await res.json();

      setData(Array.isArray(data) ? data : []);
    } catch (e) {
      console.error('Sidebar insights load failed:', e);
    }
  };

  useEffect(() => {
    loadSidebarInsights();
  }, []);

  // Get max widget count
  const maxWidgets = Math.max(...data.map((item) => item.widget_count), 0);

  // Smooth heat color (green → red)
  const getHeatColor = (count) => {
    if (maxWidgets === 0) return '#e5e7eb';

    const ratio = count / maxWidgets;

    let r,
      g,
      b = 80;

    if (ratio < 0.5) {
      // Green → Yellow
      const t = ratio / 0.5;
      r = Math.floor(255 * t);
      g = 200;
    } else {
      // Yellow → Red
      const t = (ratio - 0.5) / 0.5;
      r = 255;
      g = Math.floor(200 * (1 - t));
    }

    return `rgb(${r}, ${g}, ${b})`;
  };

  // Text color based on background
  const getTextColor = (count) => {
    const ratio = maxWidgets === 0 ? 0 : count / maxWidgets;
    return ratio > 0.6 ? '#fff' : '#111';
  };

  // Insight label
  const getLabel = (count) => {
    if (count === 0) return 'Unused area';
    if (count <= 2) return 'Low usage';
    if (count <= 5) return 'Balanced';
    return 'High usage';
  };

  // Sort by usage (high → low)
  const sortedData = [...data].sort((a, b) => b.widget_count - a.widget_count);

  return (
    <div className="wisema-insights-container">
      <h3>Sidebar Insights</h3>

      <div className="wisema-legend">
        <span>🟢 Low</span>
        <span>🟡 Medium</span>
        <span>🔴 High</span>
      </div>
      <div className="wisema-insights-container-wrap">
        {sortedData.map((item) => (
          <div
            key={item.id}
            className="wisema-insight-card"
            style={{
              background: getHeatColor(item.widget_count),
              color: getTextColor(item.widget_count),
            }}
          >
            <h3>{item.name}</h3>
            <p>{item.widget_count} widgets</p>
            <p className="wisema-label">{getLabel(item.widget_count)}</p>

            {/* Progress bar */}
            <div className="wisema-progress-bar">
              <div
                className="wisema-progress-fill"
                style={{
                  width: `${
                    maxWidgets === 0
                      ? 0
                      : (item.widget_count / maxWidgets) * 100
                  }%`,
                }}
              />
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
