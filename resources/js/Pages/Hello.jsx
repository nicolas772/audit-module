import { Head } from "@inertiajs/react";

export default function Hello({ message }) {
  return (
    <>
      <Head title="Hola mundo" />
      <div style={{
        minHeight: '100vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontFamily: 'system-ui, -apple-system, Segoe UI, Roboto'
      }}>
        <h1>{message}</h1>
      </div>
    </>
  )
}