
import AnnouncementBar from './components/layout/AnnouncementBar';
import Navbar from './components/layout/Navbar';
import Footer from './components/layout/Footer';

function App() {
  return (
    <>
      <AnnouncementBar />
      <Navbar />

      <main className="container section">
        <h1>Welcome to NOVA Shop</h1>
        <p>Discover your style.</p>
      </main>

      <Footer />
    </>
  );
}

export default App;
