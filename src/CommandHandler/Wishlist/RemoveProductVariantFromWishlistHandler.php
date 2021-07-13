<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use BitBag\SyliusWishlistPlugin\Exception\ProductVariantNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Updater\WishlistUpdaterInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveProductVariantFromWishlistHandler implements MessageHandlerInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private WishlistUpdaterInterface $wishlistUpdater;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistUpdaterInterface $wishlistUpdater
    )
    {
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistUpdater = $wishlistUpdater;
    }

    public function __invoke(RemoveProductVariantFromWishlist $removeProductVariantFromWishlist)
    {
        $variant = $this->productVariantRepository->find($removeProductVariantFromWishlist->getProductVariantIdValue());
        $wishlist = $this->wishlistRepository->findByToken($removeProductVariantFromWishlist->getWishlistTokenValue());

        if(null === $variant) {
            throw new ProductVariantNotFoundException(
                sprintf("The Product %s does not exist", $removeProductVariantFromWishlist->getProductVariantIdValue())
            );
        }

        $this->wishlistUpdater->removeProductVariantFromWishlist($wishlist, $variant);
    }
}