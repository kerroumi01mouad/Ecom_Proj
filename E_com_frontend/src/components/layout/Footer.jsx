
function Footer() {
  return (
    <footer className="footer">
      <div className="footer-brand">
        <h2>NOVA</h2>
        <p>Style that speaks for you.</p>
      </div>

      <p className="footer-copy">
        © {new Date().getFullYear()} NOVA Shop. All rights reserved.
      </p>
    </footer>
  );
}

export default Footer;
