import { useWidgets } from './context';
import './search.css';
export default function SearchBar() {
  const { searchQuery, setSearchQuery } = useWidgets();

  return (
    <div className="wisema-search-wrapper">
      <div className="wisema-search-box">
        <input
          type="text"
          value={searchQuery}
          placeholder="Search widgets..."
          onChange={(e) => setSearchQuery(e.target.value)}
          className="wisema-search-input"
        />
      </div>
    </div>
  );
}
