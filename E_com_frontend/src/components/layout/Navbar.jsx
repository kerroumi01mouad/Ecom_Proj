
import { useState } from 'react';

function Navbar() {
  const [menuOpen, setMenuOpen] = useState(false);

  return (
    <nav className="navbar">
      <a href="/" className="navbar-logo">
        NOVA
      </a>

      <button
        className="menu-toggle"
        onClick={() => setMenuOpen(!menuOpen)}
        aria-label="Toggle navigation"
      >
        ☰
      </button>

      <div className={`navbar-links ${menuOpen ? 'menu-open' : ''}`}>
        <a href="/" onClick={() => setMenuOpen(false)}>Home</a>
        <a href="/products" onClick={() => setMenuOpen(false)}>
          Shop
        </a>
        <a href="/categories" onClick={() => setMenuOpen(false)} >Categories</a>
      </div>

      <div className="navbar-actions">
        <a href="/login">Account</a>
        <a href="/cart">Cart</a>
      </div>
    </nav>
  );
}

export default Navbar;
