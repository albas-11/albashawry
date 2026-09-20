<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-[#920a69] border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7a0857] focus:bg-[#7a0857] active:bg-[#6b064c] focus:outline-none focus:ring-2 focus:ring-[#920a69] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
