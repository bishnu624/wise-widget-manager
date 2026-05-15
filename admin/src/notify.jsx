import { useWidgets } from './context';
import './notice.css';
export default function Notifications() {
  const { notifications } = useWidgets();

  if (!notifications.length) return null;

  return (
    <div className="wisema-notify-wrapper">
      {notifications.map((n) => (
        <div key={n.id} className={`wisema-notice wisema-${n.type}`}>
          {n.message}
        </div>
      ))}
    </div>
  );
}
